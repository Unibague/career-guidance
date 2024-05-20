<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormAnswerAreasResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_answer_areas_result', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_answer_id')->constrained();
            $table->unsignedBigInteger('academic_area_id');
            $table->foreign('academic_area_id')->references('id')->on('academic_areas');
            $table->bigInteger('total_available_points');
            $table->bigInteger('score');
            $table->decimal('interest_percentage',4,2)->nullable()->default(00.00);
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
        Schema::dropIfExists('form_answer_areas_result');
    }
}
