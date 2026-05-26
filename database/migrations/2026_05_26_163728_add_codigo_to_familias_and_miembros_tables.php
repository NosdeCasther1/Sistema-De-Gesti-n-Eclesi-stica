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
        Schema::table('familias', function (Blueprint $table) {
            $table->string('codigo_familia', 3)->nullable()->unique()->after('id');
        });

        Schema::table('miembros', function (Blueprint $table) {
            $table->string('codigo_miembro', 5)->nullable()->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn('codigo_familia');
        });

        Schema::table('miembros', function (Blueprint $table) {
            $table->dropColumn('codigo_miembro');
        });
    }
};
