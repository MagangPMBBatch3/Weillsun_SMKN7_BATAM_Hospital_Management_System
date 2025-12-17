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
        Schema::create('log_stok_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')
                  ->constrained('obat')
                  ->cascadeOnDelete();

            // stok masuk saja
            $table->enum('jenis', ['MASUK']);

            $table->integer('jumlah');

            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');

            // FK langsung ke pembelian_obat
            $table->foreignId('referensi_id')
                  ->constrained('pembelian_obat')
                  ->cascadeOnDelete();

            $table->timestamp('created_at')->useCurrent();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_stok_obat');
    }
};
