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
        Schema::create('detail_pembayaran_pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')
            ->nullable()
                  ->constrained('pembayaran_pasien')
                  ->onDelete('cascade');
                  
            $table->foreignId('kunjungan_id')
            ->nullable()
                  ->constrained('kunjungan')
                  ->onDelete('cascade');

            $table->foreignId('resep_id')
            ->nullable()
                  ->constrained('resep_obat')
                  ->onDelete('cascade');

            $table->foreignId('lab_id')
            ->nullable()
                  ->constrained('lab_pemeriksaan')
                  ->onDelete('cascade');

            $table->foreignId('radiologi_id')
            ->nullable()
                  ->constrained('radiologi')
                  ->onDelete('cascade');

            $table->foreignId('inap_id')
            ->nullable()
                  ->constrained('rawat_inap')
                  ->onDelete('cascade');

            $table->enum('tipe_biaya', [
                'konsultasi', 
                'obat', 
                'lab', 
                'radiologi', 
                'rawat_inap', 
                'lainnya'
            ]);

            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembayaran_pasien');
    }
};
