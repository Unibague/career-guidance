<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormAnswerResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_answer_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_answer_id')->constrained();
            $table->string('academic_program_code');
            $table->foreign('academic_program_code')->references('code')->on('academic_programs');
            $table->integer("result");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_answer_results');
    }
}
