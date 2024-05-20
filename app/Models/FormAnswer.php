<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormAnswer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function form(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public static function createFormFromRequest(Request $request, Form $form, $userInfo): void
    {
        $academicProgramsResult = self::getAcademicProgramsResult(json_decode(json_encode($request->input('answers'), JSON_THROW_ON_ERROR),
            false, 512, JSON_THROW_ON_ERROR));

        $academicAreasResult = self::getAcademicAreasResult($academicProgramsResult);

        User::createExternalUser($userInfo['userName'], $userInfo['identification'], $userInfo['age'], $userInfo['sex']);
        $user = DB::table('external_users')->where('identification','=',$userInfo['identification'])->first();

        //And then upsert the data in form_answers table
        $formAnswer = self::UpdateOrCreate([
            'user_id' => $user->id,
            'form_id' => $form->id],[
            'answers' => json_encode($request->input('answers')),
        ]);

        //In form_answers_result
        foreach ($academicProgramsResult as $academicProgram)
        {
            DB::table('form_answer_results')->updateOrInsert(
                ['form_answer_id' => $formAnswer->id, 'academic_program_code' => $academicProgram['academic_program_code']],
                ['result' => $academicProgram["accumulatedValue"]]);
        }
        //In form_answers_areas_result
        foreach ($academicAreasResult as $academicAreaResult)
        {
            DB::table('form_answer_areas_result')->updateOrInsert(
                ['form_answer_id' => $formAnswer->id, 'academic_area_id' => $academicAreaResult['academic_area_id']],
                ['total_available_points' => $academicAreaResult['total_area_available_points'],
                    'score' => $academicAreaResult['total_area_score'],
                    'interest_percentage' =>  ($academicAreaResult['total_area_score']/$academicAreaResult['total_area_available_points'])*100
                ]);
        }
    }

    public static function getAcademicAreasResult(array $academicPrograms): array{
        $academicAreas = AcademicArea::getAcademicAreas()->toArray();
        $academicAreasResult = [];
        foreach ($academicAreas as $academicArea){
            foreach ($academicPrograms as $academicProgram){
                if(self::findAcademicProgramInAcademicAreaArrayByKey($academicArea['academic_programs'],'code',$academicProgram['academic_program_code'])){
                    if(!isset($academicAreasResult[$academicArea['id']])) {
                        $academicAreasResult[$academicArea['id']] = [
                            'academic_area_id' => $academicArea['id'],
                            'academic_area_name' => $academicArea['name'],
                            'total_area_score' => 0,
                            'total_area_available_points' => 0];
                    }
                    // Ensure the sub-keys are set before incrementing
                    if (!isset($academicAreasResult[$academicArea['id']]['total_area_score'])) {
                        $academicAreasResult[$academicArea['id']]['total_area_score'] = 0;
                    }
                    if (!isset($academicAreasResult[$academicArea['id']]['total_area_available_points'])) {
                        $academicAreasResult[$academicArea['id']]['total_area_available_points'] = 0;
                    }
                    $academicAreasResult[$academicArea['id']]['total_area_score'] += $academicProgram['accumulatedValue'];
                    $academicAreasResult[$academicArea['id']]['total_area_available_points'] += $academicProgram['program_total_available_points'];
                }
            }
        }

        return $academicAreasResult;
    }

    public static function findAcademicProgramInAcademicAreaArrayByKey($array, $key, $value){
        foreach ($array as $element) {
            if (isset($element[$key]) && $element[$key] == $value) {
                return $element;
            }
        }
        return null; // Return null if no element is found
    }


    public static function getAcademicProgramsResult($answers): array
    {
        return self::getAcademicProgramsFromFormAnswer($answers);
    }

    public function compareByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    public static function getIndividualQuestionsResult($formAnswer): array
    {
        //This function has to return an array of every question with the result obtained for the formAnswer
        $finalQuestionsData = [];
        usort($formAnswer, function($a, $b)
        {
            return strcmp($a->name, $b->name);
        });
        foreach ($formAnswer as $question){
            $finalQuestionsData [] = $question->answer;
        }
        return $finalQuestionsData;
    }

    private static function getAcademicProgramsFromFormAnswer($formAnswers): array
    {
        $academicPrograms = [];
        try{
            foreach ($formAnswers as $answer) {

//                dd($answer);

                if (isset($academicPrograms[$answer->program_code]) === true) {
                    $academicPrograms[$answer->program_code]['totalAnswers']++;
                } else {
                    $academicPrograms[$answer->program_code]['totalAnswers'] = 1;
                    $max_option_answer = max($answer->options);
                    $academicPrograms[$answer->program_code]['max_option_answer_value'] = $max_option_answer->value;
                }

                // the academicProgram already exists at this point
                if (isset($academicPrograms[$answer->program_code]['accumulatedValue']) === true) {
                    $academicPrograms[$answer->program_code]['accumulatedValue'] += (double)$answer->answer;
                } else {
                    $academicPrograms[$answer->program_code]['accumulatedValue'] = (double)$answer->answer;
                }
                $academicPrograms[$answer->program_code]['academic_program_code'] = $answer->program_code;
            }

            foreach ($academicPrograms as $key => $academicProgram){
                $academicPrograms[$key]['program_total_available_points'] =
                    $academicProgram['totalAnswers']*$academicProgram['max_option_answer_value'];
            }

        }
        catch (\Exception $exception) {
            $message = 'Debes contestar todas las preguntas para poder enviar el formulario';
            throw new \RuntimeException($message);
        }

        return $academicPrograms;
    }
}
