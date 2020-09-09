<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCityVisitedCongenitalDisease extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_applicants', function (Blueprint $table) {
            $table->string('city_visited')->nullable();
            $table->string('congenital_disease')->nullable();
            $table->string('have_interacted', 10)->nullable();
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
            $table->dropColumn('city_visited');
            $table->dropColumn('congenital_disease');
            $table->dropColumn('have_interacted');
        });
    }
}
