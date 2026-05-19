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
        Schema::table('miembros', function (Blueprint $table) {
            $table->string('nivel_academico')->nullable()->after('ciudad');
            $table->string('profesion')->nullable()->after('nivel_academico');
            $table->string('lugar_trabajo_estudio')->nullable()->after('profesion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            $table->dropColumn(['nivel_academico', 'profesion', 'lugar_trabajo_estudio']);
        });
    }
};
