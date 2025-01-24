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
        Schema::table('societe_reservations_divers', function (Blueprint $table) {
            $table->unsignedBigInteger('id_diver')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('societe_reservations_divers', function (Blueprint $table) {
            $table->dropColumn('id_diver'); 
        });
    }
};
