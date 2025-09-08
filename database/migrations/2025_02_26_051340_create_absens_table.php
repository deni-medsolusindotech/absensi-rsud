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
        Schema::create('absens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->string('status_shift');
            $table->dateTime('jam_masuk_shift');
            $table->dateTime('jam_pulang_shift');
            $table->string('foto_masuk')->nullable();
            $table->dateTime('jam_masuk')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->dateTime('telat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
