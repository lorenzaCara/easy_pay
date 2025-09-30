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
        Schema::create('expense_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expense_id')
                ->constrained('expenses')
                ->onDelete('cascade');
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->decimal('share_amount');
            $table->enum('status', ['pending', 'paid'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_participants');
    }
};
