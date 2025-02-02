<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('societe_accomptes_reservations', function (Blueprint $table) {
            $table->boolean('paid')->nullable();
            $table->unsignedBigInteger('facture_id')->nullable()->index();

            $table->foreign('facture_id')->references('id')->on('societe_factures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('societe_accomptes_reservations', function (Blueprint $table) {
            $table->dropColumn('paid');
            $table->dropColumn('facture_id');
        });
    }
};
