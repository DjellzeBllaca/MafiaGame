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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['Day','Night']);
            $table->foreignId('game_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('killed')->nullable()->constrained('users', 'id');
            $table->foreignId('voted_out')->nullable()->constrained('users', 'id');
            $table->foreignId('saved')->nullable()->constrained('users', 'id');
            $table->foreignId('investigated')->nullable()->constrained('users', 'id');
            $table->foreignId('robbed')->nullable()->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rounds');
    }
};
