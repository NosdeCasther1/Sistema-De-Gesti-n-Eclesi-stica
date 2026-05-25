<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('elecciones', function (Blueprint $table) {
            // Control de rondas en asamblea
            $table->string('puesto_en_curso')->nullable()->after('estado');
            $table->string('pin_ronda', 10)->nullable()->after('puesto_en_curso');
        });

        Schema::table('candidatos', function (Blueprint $table) {
            // Tally anónimo segregado
            $table->unsignedInteger('votos_digitales')->default(0)->after('puesto_postulado');
            $table->unsignedInteger('votos_manuales')->default(0)->after('votos_digitales');
        });
    }

    public function down(): void {
        Schema::table('elecciones', function (Blueprint $table) {
            $table->dropColumn(['puesto_en_curso', 'pin_ronda']);
        });
        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn(['votos_digitales', 'votos_manuales']);
        });
    }
};
