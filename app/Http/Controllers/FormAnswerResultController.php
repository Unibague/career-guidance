<?php

namespace App\Http\Controllers;

use App\Exports\ResultsExport;
use App\Models\Form;
use App\Models\FormAnswer;
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


    public function showGraph(Request $request){
        $user = json_decode($request->input('user'));
        return Inertia::render('Results/Index', ['user' => ['name' => $user->userName, 'identification' => $user->identification]]);

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
