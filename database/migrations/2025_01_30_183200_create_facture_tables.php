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
        Schema::create('societe_factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_accompte')->nullable()->index();
            $table->date('date_facture')->nullable();
            $table->unsignedBigInteger('user')->nullable()->index();
            $table->integer('num_facture')->nullable();
            $table->string('link')->nullable();
            $table->uuid('id_hotel')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_accompte')->references('id')->on('societe_accomptes_reservations')->onDelete('cascade');
            $table->foreign('user')->references('ID')->on('hot_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_factures');
    }
};
