<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function formAnswers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FormAnswer::class);
    }

    public static function getCurrentForms()
    {
        return self::all();
    }

    public static function createForm($request)
    {
        return self::UpdateOrCreate(
            ['id' => $request->input('id')],
            [
                'name' => $request->input('name'),
                'description' => $request->input('description'),

            ]);
    }

    public static function getFormQuestions($formQuestions){
        $formQuestionsArray = [];
        $formQuestions = json_decode($formQuestions);
        usort($formQuestions, function($a, $b)
        {
            return strcmp($a->name, $b->name);
        });
        foreach ($formQuestions as $formQuestion){
            $formQuestionsArray [] = $formQuestion->name;
         }
        return $formQuestionsArray;
    }

    public static function findFirstOccurrence($array, $value) {
        foreach ($array as $item) {
            if ($item->name === $value) {
                return $item; // Return the first occurrence found
            }
        }
        return null; // Return null if the value is not found in the array
    }

}
