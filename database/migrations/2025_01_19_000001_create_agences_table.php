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
        Schema::create('societe_agences', function (Blueprint $table) {
            $table->id();
            $table->string('email_agence')->nullable();
            $table->string('telephone_agence')->nullable();
            $table->string('site_web_agence')->nullable();
            $table->string('nom_agence')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->text('autres_info_agence')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('text_color')->nullable();
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
        Schema::dropIfExists('societe_agences');
    }
};
