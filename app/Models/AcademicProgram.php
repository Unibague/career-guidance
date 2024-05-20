<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicProgram extends Model
{


    //En la tabla academic_program_questions se busca las coindicencias que haya en la columna code de la tabla academic_programs
    public function academicProgramQuestions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcademicProgramQuestion::class, 'academic_program_code','code');
    }

    public function academicArea(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicArea::class);
    }


    public static function createOrUpdateFromArray($academicPrograms){
        $upsertData = [];
        foreach ($academicPrograms as $academicProgram) {
            if (preg_match('/^\d/', $academicProgram->program_code)) {
                if($academicProgram->program_code === "35"){
                    $academicProgram->program_name = "DISENO";
                }
                $upsertData[] = [
                    'code' => $academicProgram->program_code,
                    'name' => ucwords(strtolower($academicProgram->program_name)),
                ];
                DB::table('academic_programs')->upsert($upsertData, 'code', ['name']);
            }
        }
    }

    public static function getAcademicPrograms(){
        return self::with('academicProgramQuestions')->orderBy('name','asc')->get();
    }


    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Use 'code' field for route binding instead of 'id'
        $field = $field ?: $this->getRouteKeyName();

        return $this->where($field, $value)->firstOrFail();
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        // Use 'code' field as the route key
        return 'code';
    }

    use HasFactory;
}
