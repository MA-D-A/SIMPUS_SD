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
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah', 150);
            $table->string('alamat_sekolah')->nullable();
            $table->string('nama_kepala_sekolah', 150)->nullable();
            $table->string('nip_kepala_sekolah', 30)->nullable();
            $table->unsignedTinyInteger('lama_pinjam')->default(7);
            $table->unsignedTinyInteger('maks_pinjam')->default(2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
