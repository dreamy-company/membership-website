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
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->after('member_id');
            $table->integer('transaction_id')->nullable()->after('user_id');
            $table->time('transaction_time')->nullable()->after('transaction_date');
            $table->string('LevelMember')->nullable()->after('balance');
            $table->double('BonusPercent')->nullable()->after('LevelMember');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
