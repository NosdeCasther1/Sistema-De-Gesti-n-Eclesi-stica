<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votos', function (Blueprint $table) {
            $table->enum('modalidad', ['autoservicio', 'asistido'])->default('autoservicio')->after('miembro_id');
            $table->foreignId('registrado_por_admin_id')->nullable()->after('modalidad')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('votos', function (Blueprint $table) {
            $table->dropForeign(['registrado_por_admin_id']);
            $table->dropColumn(['modalidad', 'registrado_por_admin_id']);
        });
    }
};
