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
        Schema::table('hot_users', function (Blueprint $table) {
            $table->foreign('id_hotel')->references('id_hotel')->on('hotel')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hot_users', function (Blueprint $table) {
            $table->dropForeign(['id_hotel']); // Supprime la clé étrangère
        });
    }
};
