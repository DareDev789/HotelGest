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
        Schema::create('societe_hotels', function (Blueprint $table) {
            $table->uuid('id_hotel')->primary(); // Utilisation d'un UUID
            $table->string('nom_etablissement')->nullable();
            $table->string('gerant_etablissement')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('site_web')->nullable();
            $table->date('date_inscription')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('nom_societe')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('societe_users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('nom')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->string('niveau_user')->nullable();
            $table->boolean('validated')->nullable();
            $table->string('auth')->nullable();
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
        Schema::dropIfExists('societe_users');
        Schema::dropIfExists('societe_hotels');
    }
};
