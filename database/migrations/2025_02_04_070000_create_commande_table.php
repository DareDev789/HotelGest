<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('societe_commande', function (Blueprint $table) {
            $table->uuid('id_commande')->primary();
            $table->unsignedBigInteger('id_client')->nullable();
            $table->string('nom_client')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->unsignedBigInteger('id_agence')->nullable();
            $table->string('nom_agence')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_client')->references('id')->on('societe_clients')->onDelete('cascade');
            $table->foreign('id_agence')->references('id')->on('societe_agences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_commande');
    }
};
