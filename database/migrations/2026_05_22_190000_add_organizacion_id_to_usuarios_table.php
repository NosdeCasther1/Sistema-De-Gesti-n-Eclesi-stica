<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('organizacion_id')
                  ->nullable()
                  ->after('password')
                  ->constrained('organizaciones')
                  ->nullOnDelete();

            $table->rememberToken()->after('organizacion_id');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['organizacion_id']);
            $table->dropColumn('organizacion_id');
            $table->dropColumn('remember_token');
        });
    }
};
