<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AcademicArea extends Model
{

    protected $guarded = ['academic_programs'];

    public function academicPrograms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcademicProgram::class);
    }

    public static function getAcademicAreas(){
        return self::with('academicPrograms')->get();
    }

    use HasFactory;
}
