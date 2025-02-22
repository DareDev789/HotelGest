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
        Schema::create('societe_accomptes_commandes', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_commande')->nullable()->index();
            $table->uuid('id_hotel')->nullable()->index();
            $table->decimal('montant', 10, 2)->nullable();
            $table->unsignedBigInteger('save_by')->nullable()->index();
            $table->boolean('paid')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('id_hotel')->references('id_hotel')->on('societe_hotels')->onDelete('cascade');
            $table->foreign('id_commande')->references('id_commande')->on('societe_commande')->onDelete('cascade');
            $table->foreign('save_by')->references('id')->on('societe_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societe_accomptes_commandes');
    }
};
