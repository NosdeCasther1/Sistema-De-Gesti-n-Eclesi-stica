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
        if (Schema::hasColumn('miembros', 'ciudad')) {
            Schema::table('miembros', function (Blueprint $table) {
                $table->renameColumn('ciudad', 'municipio');
            });
        }

        Schema::table('miembros', function (Blueprint $table) {
            if (!Schema::hasColumn('miembros', 'zona')) {
                $table->string('zona', 50)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('miembros', 'departamento')) {
                $table->string('departamento', 100)->nullable()->after('municipio'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            $table->dropColumn(['zona', 'departamento']);
            $table->renameColumn('municipio', 'ciudad');
        });
    }
};
