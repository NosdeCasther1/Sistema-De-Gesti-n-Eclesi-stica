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
        Schema::create('certificado_presentacions', function (Blueprint $table) {
            $table->id();
            $table->string('nino_nombre');
            $table->date('nino_fecha_nacimiento')->nullable();
            $table->string('lugar_nacimiento')->nullable();
            $table->foreignId('padre_id')->nullable()->constrained('miembros')->nullOnDelete();
            $table->foreignId('madre_id')->nullable()->constrained('miembros')->nullOnDelete();
            $table->date('fecha_presentacion');
            $table->string('pastor_oficiante')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificado_presentacions');
    }
};
