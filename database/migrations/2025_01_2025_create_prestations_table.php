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
        Schema::create('societe_prestations', function (Blueprint $table) {
            $table->id();
            $table->string('prestation')->nullable();
            $table->float('prix_prestation')->nullable();
            $table->string('autre_info_prestation')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_prestations');
    }
};
