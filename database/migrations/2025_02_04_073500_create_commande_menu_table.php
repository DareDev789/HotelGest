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
        Schema::create('societe_details_commande_menu', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_commande')->nullable();
            $table->integer('quantite')->nullable();
            $table->unsignedBigInteger('id_menu')->nullable();
            $table->float('prix_menu')->nullable();
            $table->string('nom_menu')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->unsignedBigInteger('save_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_menu')->references('id')->on('societe_menu')->onDelete('cascade');
            $table->foreign('id_commande')->references('id_commande')->on('societe_commande')->onDelete('cascade');
            $table->foreign('save_by')->references('id')->on('societe_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_details_commande_menu');
    }
};
