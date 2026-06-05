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
            $table->string('zona', 50)->nullable()->after('direccion');
            $table->string('municipio', 100)->nullable()->after('zona');
            $table->string('departamento', 100)->nullable()->after('municipio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropColumn(['zona', 'municipio', 'departamento']);
        });
    }
};
