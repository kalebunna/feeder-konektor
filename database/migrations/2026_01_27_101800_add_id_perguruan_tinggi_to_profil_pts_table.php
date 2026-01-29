<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_pts', function (Blueprint $table) {
            $table->uuid('id_perguruan_tinggi')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_pts', function (Blueprint $table) {
            $table->dropColumn('id_perguruan_tinggi');
        });
    }
};
