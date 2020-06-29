<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPeopeStatusToTableRdtApplicants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_applicants', function (Blueprint $table) {
            $table->tinyInteger('people_status')
                  ->after('symptoms_activity')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rdt_applicants', function (Blueprint $table) {
            $table->dropColumn('people_status');
        });
    }
}
