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
        Schema::create('societe_details_prestations', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_reservation')->nullable();
            $table->integer('nb_personne')->nullable();
            $table->unsignedBigInteger('id_prestation')->nullable();
            $table->float('prix_prestation')->nullable();
            $table->date('date_in')->nullable();
            $table->date('date_out')->nullable();
            $table->string('prestation')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_prestation')->references('id')->on('societe_prestations')->onDelete('cascade');
            $table->foreign('id_reservation')->references('id_reservation')->on('societe_reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_details_prestations');
    }
};
