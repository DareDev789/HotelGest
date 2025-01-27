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
        Schema::create('societe_type_bungalows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bungalow')->nullable()->index();
            $table->string('type_bungalow')->nullable();
            $table->decimal('prix_particulier', 10, 2)->nullable();
            $table->decimal('prix_agence', 10, 2)->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_bungalow')->references('id')->on('societe_bungalows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_type_bungalows');
    }
};
