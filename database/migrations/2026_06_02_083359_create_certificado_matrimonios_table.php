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
        Schema::create('certificado_matrimonios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esposo_id')->constrained('miembros')->cascadeOnDelete();
            $table->foreignId('esposa_id')->constrained('miembros')->cascadeOnDelete();
            $table->date('fecha_matrimonio');
            $table->string('pastor_oficiante')->nullable();
            $table->string('testigo_1')->nullable();
            $table->string('testigo_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificado_matrimonios');
    }
};
