<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('societe_reservations', function (Blueprint $table) {
            $table->dropColumn('taxe');
            $table->string('taxe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('societe_reservations', function (Blueprint $table) {
            $table->string('taxe')->nullable();
            $table->dropColumn('taxe');
        });
    }
};
