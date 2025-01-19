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
        Schema::create('societe_clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom_client')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->text('autres_info_client')->nullable();
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
        Schema::dropIfExists('societe_clients');
    }
};
