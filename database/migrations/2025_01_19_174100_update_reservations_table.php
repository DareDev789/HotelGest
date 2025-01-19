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
            $table->string('etat_reservation')->nullable();
            $table->dropColumn('date_debut');
            $table->dropColumn('date_fin');
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
            $table->dropColumn('etat_reservation'); // Remove the added column
            $table->date('date_debut')->nullable(); // Re-add the dropped columns
            $table->date('date_fin')->nullable();
        });
    }
};
