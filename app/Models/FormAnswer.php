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

        User::createExternalUser($userInfo['userName'], $userInfo['identification'], $userInfo['age'], $userInfo['sex'], $userInfo['phone']);

        $user = DB::table('external_users')->where('identification','=',$userInfo['identification'])->first();

        //And then upsert the data in form_answers table
        $formAnswer = self::UpdateOrCreate([
            'user_id' => $user->id,
            'form_id' => $form->id],[
            'answers' => json_encode($request->input('answers')),
        ]);

        foreach ($academicProgramsResult as $academicProgram)
        {
            DB::table('form_answer_results')->updateOrInsert(
                ['form_answer_id' => $formAnswer->id, 'academic_program_code' => $academicProgram['academic_program_code']],
                ['result' => $academicProgram["accumulatedValue"]]);
        }


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

                if (isset($academicPrograms[$answer->program_code]) === true) {
                    $academicPrograms[$answer->program_code]['totalAnswers']++;
                } else {
                    $academicPrograms[$answer->program_code]['totalAnswers'] = 1;
                }

                // the competence already exist at this point
                if (isset($academicPrograms[$answer->program_code]['accumulatedValue']) === true) {
                    $academicPrograms[$answer->program_code]['accumulatedValue'] += (double)$answer->answer;
                } else {
                    $academicPrograms[$answer->program_code]['accumulatedValue'] = (double)$answer->answer;
                }

                $academicPrograms[$answer->program_code]['academic_program_code'] = $answer->program_code;

            }
        }
        catch (\Exception $exception) {
            $message = 'Debes contestar todas las preguntas para poder enviar el formulario';
            throw new \RuntimeException($message);
        }

        return $academicPrograms;
    }
}
