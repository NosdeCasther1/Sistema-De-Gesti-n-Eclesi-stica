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
        if (!Schema::hasColumn('financial_accounts', 'deleted_at')) {
            Schema::table('financial_accounts', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('financial_categories', 'deleted_at')) {
            Schema::table('financial_categories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('categoria_financieras', 'deleted_at')) {
            Schema::table('categoria_financieras', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('financial_accounts', 'deleted_at')) {
            Schema::table('financial_accounts', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('financial_categories', 'deleted_at')) {
            Schema::table('financial_categories', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('categoria_financieras', 'deleted_at')) {
            Schema::table('categoria_financieras', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
