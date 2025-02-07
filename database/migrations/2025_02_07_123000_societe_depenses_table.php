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
        Schema::create('societe_depenses', function (Blueprint $table) {
            $table->id();
            $table->string('categorie')->nullable();
            $table->float('montant')->nullable();
            $table->string('description')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->unsignedBigInteger('save_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('save_by')->references('id')->on('societe_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_depenses');
    }
};
