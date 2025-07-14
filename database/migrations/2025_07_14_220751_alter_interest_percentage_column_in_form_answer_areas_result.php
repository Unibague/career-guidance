<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInterestPercentageColumnInFormAnswerAreasResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_answer_areas_result', function (Blueprint $table) {
            $table->decimal('interest_percentage', 5, 2)->nullable()->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_answer_areas_result', function (Blueprint $table) {
            $table->decimal('interest_percentage', 4, 2)->nullable()->default(0.00)->change();
        });
    }
}
