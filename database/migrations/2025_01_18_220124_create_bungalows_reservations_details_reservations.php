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
        Schema::create('societe_bungalows', function (Blueprint $table) {
            $table->id();
            $table->string('designation_bungalow')->nullable();
            $table->string('type_bungalow')->nullable();
            $table->string('num_bungalow')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->integer('tri')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
        });

        Schema::create('societe_reservations', function (Blueprint $table) {
            $table->uuid('id_reservation')->primary(); // Correction de l'espace supplémentaire
            $table->unsignedBigInteger('id_client')->nullable();
            $table->unsignedBigInteger('id_agence')->nullable();
            $table->date('date_debut')->nullable(); // Utilisation de `date` au lieu de `string` pour les dates
            $table->date('date_fin')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->string('type_client')->nullable();
            $table->unsignedBigInteger('reserv_par')->nullable();
            $table->unsignedBigInteger('annule_par')->nullable();
            $table->integer('remise')->nullable();
            $table->integer('taux')->nullable();
            $table->integer('tva')->nullable();
            $table->string('devise')->nullable();
            $table->longText('notes')->nullable();
            $table->integer('taxe')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_client')->references('id')->on('societe_clients')->onDelete('cascade');
            $table->foreign('id_agence')->references('id')->on('societe_agences')->onDelete('cascade');
        });

        Schema::create('societe_details_reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_reservation')->nullable(); // Correction pour correspondre à l'UUID
            $table->unsignedBigInteger('id_bungalow')->nullable();
            $table->string('type_bungalow')->nullable();
            $table->integer('prix_bungalow')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->integer('nb_personne')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_reservation')->references('id_reservation')->on('societe_reservations')->onDelete('cascade');
            $table->foreign('id_bungalow')->references('id')->on('societe_bungalows')->onDelete('cascade');
            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression dans l'ordre inverse des dépendances
        Schema::dropIfExists('societe_details_reservations');
        Schema::dropIfExists('societe_reservations');
        Schema::dropIfExists('societe_bungalows');
    }
};
