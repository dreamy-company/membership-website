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
        Schema::create('bonus_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('from_member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->integer('level');
            $table->double('percentage');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_logs');
    }
};
