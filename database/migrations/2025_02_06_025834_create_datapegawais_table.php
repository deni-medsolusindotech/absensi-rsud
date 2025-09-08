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
        Schema::create('datapegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Id_user');
            $table->string('nik');
            $table->string('nama lengkap');
            $table->string('tempat lahir');
            $table->string('tanggal lahir');
            $table->string('jabatan');
            $table->string('no hp');
            $table->string('email');
            $table->string('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datapegawais');
    }
};
