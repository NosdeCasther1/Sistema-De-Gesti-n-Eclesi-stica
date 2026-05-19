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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('account_id')->constrained('financial_accounts')->onUpdate('cascade')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('financial_categories')->onUpdate('cascade')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->restrictOnDelete();

            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 15, 2)->unsigned();
            $table->date('transaction_date');

            $table->string('description');
            $table->string('reference_number')->nullable();
            $table->string('proof_path')->nullable();

            $table->string('status', 30)->default('completed');

            $table->timestamps();

            $table->index('transaction_date');
            $table->index(['account_id', 'transaction_date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
