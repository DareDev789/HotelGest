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
        Schema::create('societe_produit_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_produit')->nullable();
            $table->integer('stock')->nullable();
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_produit')->references('id')->on('societe_produit')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('societe_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_produit_stock');
    }
};
