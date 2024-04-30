<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicProgramQuestion extends Model
{

    protected $guarded = [];

    public function academicProgram(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class,'academic_program_code');
    }

    use HasFactory;
}
