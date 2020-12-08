<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByUpdatedByAtEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_events', function (Blueprint $table) {
            $table->string('created_by', 36)->after('created_at')->nullable();
            $table->string('updated_by', 36)->after('updated_at')->nullable();
            $table->index(['created_by' , 'updated_by']);
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
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropIndex(['created_by','updated_by']);
        });
    }
}
