<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusSinkronToRdtInvitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_invitations', function (Blueprint $table) {
            $table->datetime('synchronization_at')->after('notified_result_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rdt_invitations', function (Blueprint $table) {
            $table->dropColumn('synchronization_at');
        });
    }
}
