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
        Schema::create('societe_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_categorie')->nullable();
            $table->string('nom_menu')->nullable();
            $table->decimal('prix_menu', 10, 2)->nullable();
            $table->string('autres_info_menu')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_categorie')->references('id')->on('societe_categorie_menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_menu');
    }
};
