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
        Schema::create('transaccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categoria_financieras')->onDelete('cascade');
            $table->foreignId('miembro_id')->nullable()->constrained('miembros')->onDelete('set null');
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->string('descripcion')->nullable();
            $table->enum('metodo_pago', ['Efectivo', 'Transferencia', 'Tarjeta'])->default('Efectivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaccions');
    }
};
