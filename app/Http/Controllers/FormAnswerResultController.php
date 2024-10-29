<?php

namespace App\Http\Controllers;

use App\Exports\ResultsExport;
use App\Exports\ResultsViewExport;
use App\Models\AcademicArea;
use App\Models\Form;
use App\Models\FormAnswer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class FormAnswerResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $identification = $request->input('identification');

        if($identification !== null){
            $academicProgramsResults = DB::table('external_users as eu')->select(['ap.name','far.academic_program_code','far.result as value'])
                ->where('eu.identification','=',$identification)
                ->join('form_answers as fa','eu.id','=','fa.user_id')
                ->join('form_answer_results as far','fa.id','=','far.form_answer_id')
                ->join('academic_programs as ap','far.academic_program_code','=','ap.code')->orderBy('result','desc')->get()->take(5);

            $values = [];
            $labels = [];
            foreach ($academicProgramsResults as $academicProgramResult){
                $values [] = $academicProgramResult->value;
                $labels [] = $academicProgramResult->name;
            }
            $finalData =  ['results' => $values, 'labels' => $labels];
            return response()->json($finalData);
        }


        //Query all the users, then have to retrieve all the users in an array first
        $users = DB::table('external_users as eu')->get()->toArray();
        $usersIdentification = array_unique(array_column($users,'identification'));
        $finalData = [];
        foreach ($usersIdentification as $userIdentification){
            $userAcademicProgramsResults =  DB::table('external_users as eu')->select(['ap.name','far.academic_program_code','far.result as value'])
                ->where('eu.identification','=',$userIdentification)
                ->join('form_answers as fa','eu.id','=','fa.user_id')
                ->join('form_answer_results as far','fa.id','=','far.form_answer_id')
                ->join('academic_programs as ap','far.academic_program_code','=','ap.code')->orderBy('ap.name','asc')->get()->toArray();
            if(count($userAcademicProgramsResults)>0){
                $finalData [] = (object) ['identification' => $userIdentification, 'academic_programs_result' => $userAcademicProgramsResults];
            }
        }
        return $finalData;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function getAcademicAreasResult(Request $request){
        $identification = $request->input('identification');

        if($identification !== null){
            $academicAreasResults = DB::table('external_users as eu')->select(['aa.name', 'aa.message',
                'far.academic_area_id','far.interest_percentage as value'])
                ->where('eu.identification','=',$identification)
                ->join('form_answers as fa','eu.id','=','fa.user_id')
                ->join('form_answer_areas_result as far','fa.id','=','far.form_answer_id')
                ->join('academic_areas as aa','far.academic_area_id','=','aa.id')->get();

            $values = [];
            $labels = [];
            $academicAreasInfo = [];
            foreach ($academicAreasResults as $academicAreaResult){
                $values [] = $academicAreaResult->value;
                $labels [] = $academicAreaResult->name;
                $academicAreasInfo [] = ['academic_area_name' =>  $academicAreaResult->name
                    ,'message' => $academicAreaResult->message];
            }
            $finalData =  ['results' => $values, 'labels' => $labels, 'basicInfo' => $academicAreasInfo];
            return response()->json($finalData);
        }
    }


    public function downloadSpecificReport()
    {

    //First get the headers (the questions) of the specific form.
    $form = DB::table('forms')->first();
    $academicProgramsQuestionsArray = Form::getFormQuestions($form->questions);

    $headers = ['Sexo', 'Edad'];
    $headers = array_merge($headers, $academicProgramsQuestionsArray);

    $formAnswers = DB::table('form_answers')->where('form_id','=',$form->id)->get();
    $finalData = [];

       foreach ($formAnswers as $formAnswer){
           //First, get the attributes from the user
           $userInfo = DB::table('external_users')->where('id','=',$formAnswer->user_id)->first();
           $userData = [];
           $userData [] = $userInfo->sex;
           $userData [] = $userInfo->age;

           $questionsResult = FormAnswer::getIndividualQuestionsResult(json_decode($formAnswer->answers, false, 512, JSON_THROW_ON_ERROR));
           $finalFormAnswerData = array_merge($userData, $questionsResult);
           $finalData [] = $finalFormAnswerData;
        }

        return Excel::download(new ResultsExport($finalData, $headers), 'Test_Vocacional.xlsx');
    }

    public function generateUserResultsPDF(Request $request){

        $academicAreas = AcademicArea::all();
        $data = $request->input('data');
        $userName = $data['userName'];
        $charts = $data['charts'];

        $pieChart = $charts['pieChart'];
        $barChart = $charts['barChart'];

        $pdf = Pdf::loadView('charts', compact('pieChart', 'barChart', 'academicAreas', 'userName'));
        $pdf->setPaper('A4', 'portrait')->setWarnings(false);

        return $pdf->download('resultado.pdf');

    }


    public function generateUserReportPDF(Request $request)
    {
        $userName = $request->input('userName');
        $academicAreas = AcademicArea::all();
        $pieChartLabels = $request->input('pieChartLabels');
        $pieChartResults = $request->input('pieChartResults');



        // Decode the JSON strings
        $labels = json_decode($pieChartLabels);
        $results = json_decode($pieChartResults);

        $barChartLabels = json_decode($request->input('barChartLabels'));
        $barChartResults = json_decode($request->input('barChartResults'));


        $pieChartFormattedResults = array_map(function($value) {
            // Convert string to float
            $numValue = floatval($value);
        // Check if it's a whole number
            if (floor($numValue) == $numValue) {
                return number_format($numValue, 0, '.', ''); // No decimal places
            } else {
                return number_format($numValue, 2, '.', ''); // Two decimal places
            }
        }, $results);

        // Construct the chart configuration array
        $pieChartConfig = [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Grado de interés (%)',
                    'backgroundColor' => ['#3498db', '#2ecc71', '#f1c40f', '#e67e22', '#9b59b6', '#00B0FF'],
                    'data' => $pieChartFormattedResults,
                ]],
            ],
            'options' => [
                'plugins' => [
                    'datalabels' => [
                        'display' => true,
                        'color' => '#fff',
                        'font' => [
                            'weight' => 'bold',
                            'size' => 17,
                        ],
                        'formatter' => function($value) {
                            return $value . '%';
                        },
                    ],
                ],
            ],
        ];


// Format the bar chart configuration
        $barChartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $barChartLabels,
                'datasets' => [[
                    'label' => 'Nivel de interés',
                    'data' => $barChartResults,
                    'backgroundColor' => ['#dc143c', '#00bcd4', '#32cd32', '#ffbf00', '#6a5acd'],
                ]],
            ],
            'options' => [
                'plugins' => [
                    'datalabels' => [
                        'display' => true,
                        'color' => '#fff',
                        'font' => [
                            'weight' => 'bold',
                            'size' => 17,
                        ],
                        'anchor' => 'end',
                        'align' => 'start',
                        'offset' => 0,
                        'clip' => false,
                    ],
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'scales' => [
                    'x' => [
                        'display' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Programa académico',
                            'color' => 'black',
                            'font' => [
                                'size' => 15,
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                    'y' => [
                        'display' => true,
                        'ticks' => [
                            'min' => 0, // Set the minimum value to 0 explicitly
                            'stepSize' => 1, // Optional: set the step size
                        ],
                        'title' => [
                            'display' => true,
                            'text' => 'Nivel de interés',
                            'color' => 'black',
                            'font' => [
                                'size' => 15,
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                ],
            ],
        ];




        // Encode chart configurations
        $encodedPieChartConfig = urlencode(json_encode($pieChartConfig));
        $encodedBarChartConfig = urlencode(json_encode($barChartConfig));

        // Create the chart URLs
        $pieChartUrl = "https://quickchart.io/chart?c=" . $encodedPieChartConfig;
        $barChartUrl = "https://quickchart.io/chart?c=" . $encodedBarChartConfig;

        // Pass chart URLs to the Blade view for PDF generation
        return view('pdfReport', [
            'userName' => $userName,
            'academicAreas' => $academicAreas,
            'pieChartUrl' => $pieChartUrl,
            'barChartUrl' => $barChartUrl,
        ]);

    }


    public function testDownloadSpecificReport()
    {
        //First get the headers (the questions) of the specific form.
        $form = DB::table('forms')->first();

        $academicProgramsQuestionsArray = Form::getFormQuestions($form->questions);

        $formAnswers = DB::table('form_answers')->where('form_id','=',$form->id)->get();
        $tableData = [];

        foreach ($formAnswers as $formAnswer){
            //First, get the attributes from the user
            $userInfo = DB::table('external_users')->where('id','=',$formAnswer->user_id)->first();

            // Every answer is going to be stored in an array called $rowData
            $rowData = [];
            //First we insert the name, sex and age of every user
            $rowData [] = $userInfo->name;
            $rowData [] = $userInfo->sex;
            $rowData [] = $userInfo->age;
            $rowData [] = $userInfo->email;
            $rowData [] = $userInfo->phone;

            $formAnswerAsJson = (json_decode($formAnswer->answers, false, 512, JSON_THROW_ON_ERROR));
            //Now we insert the results for every question in case the question exists in the form_answer
            foreach ($academicProgramsQuestionsArray as $question){
                $result = Form::findFirstOccurrence($formAnswerAsJson, $question);
                if ($result){
                    $rowData[] = $result->answer;
                    continue;
                }
                $rowData [] = "";
            }
            $tableData[] = $rowData;
        }

        //Now that every answer was collected and correctly mapped, just format the AcademicProgramsQuestions as requested
        $academicProgramsQuestionsArrayFormatted = Form::getFormQuestionsFormatted($form->questions);

        return Excel::download(new ResultsViewExport($academicProgramsQuestionsArrayFormatted, $tableData), 'Test_Vocacional.xlsx');
    }


    public function showGraph(Request $request){
        $name = ($request->input('name'));
        $identification = ($request->input('identification'));
        return Inertia::render('Results/Index', ['user' => ['name' => $name, 'identification' => $identification]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
