<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullablePhoneNumberToExternalUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('external_users', function (Blueprint $table) {
            $table->bigInteger('phone')->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('external_users', function (Blueprint $table) {
            $table->bigInteger('phone')->default(0)->nullable(false)->change();
        });
    }
}
