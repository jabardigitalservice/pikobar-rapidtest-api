<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderNotifToTableInvitation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rdt_invitations', function (Blueprint $table) {
            $table->string('notified_result_by', 50)->after('notified_result_at')->nullable();
            $table->string('notified_by', 50)->after('notified_at')->nullable();
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
            $table->dropColumn('notified_result_by');
            $table->dropColumn('notified_by');
        });
    }
}
