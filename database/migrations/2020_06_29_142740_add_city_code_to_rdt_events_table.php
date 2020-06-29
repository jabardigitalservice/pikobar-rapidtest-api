<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityCodeToRdtEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_events', function (Blueprint $table) {
            $table->string('province_code')->after('event_name')->nullable();
            $table->string('city_code')->after('province_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rdt_events', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'city_code']);
        });
    }
}
