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
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('accounts')->cascadeOnDelete();
            $table->string('nisn')->index();
            $table->enum('status', ['belum', 'mulai', 'selesai'])->default('belum');
            $table->enum('tahap', ['umum', 'jeda', 'kejuruan'])->default('umum');
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('waktu_selesai_umum')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
