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
            $table->string('lugar_conversion')->nullable();
            $table->date('fecha_conversion')->nullable();
            $table->foreignId('conyuge_id')->nullable()->constrained('miembros')->nullOnDelete();
            $table->boolean('bautizado_agua')->default(false);
            $table->boolean('bautismo_espiritu_santo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            $table->dropForeign(['conyuge_id']);
            $table->dropColumn([
                'lugar_conversion',
                'fecha_conversion',
                'conyuge_id',
                'bautizado_agua',
                'bautismo_espiritu_santo'
            ]);
        });
    }
};
