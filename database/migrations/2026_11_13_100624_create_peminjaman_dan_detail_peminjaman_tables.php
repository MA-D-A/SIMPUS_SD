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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status', ['dipinjam', 'selesai'])->default('dipinjam');
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_jatuh_tempo']);
        });

        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->foreignId('buku_id')->constrained('buku')->restrictOnDelete();
            $table->date('tanggal_kembali')->nullable();
            $table->timestamps();

            $table->unique(['peminjaman_id', 'buku_id']);
            $table->index(['buku_id', 'tanggal_kembali']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('peminjaman');
    }
};
