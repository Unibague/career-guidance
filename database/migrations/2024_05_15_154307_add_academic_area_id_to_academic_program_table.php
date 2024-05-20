<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAcademicAreaIdToAcademicProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('academic_programs', function (Blueprint $table) {
            $table->foreignId('academic_area_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academic_programs', function (Blueprint $table) {
            $table->dropColumn('academic_area_id');
        });
    }
}
