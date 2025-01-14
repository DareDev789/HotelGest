<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotel', function (Blueprint $table) {
            $table->softDeletes(); // Ajoute la colonne 'deleted_at'
            $table->timestamps();  // Ajoute les colonnes 'created_at' et 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Supprime la colonne 'deleted_at'
            $table->dropTimestamps();  // Supprime les colonnes 'created_at' et 'updated_at'
        });
    }
};
