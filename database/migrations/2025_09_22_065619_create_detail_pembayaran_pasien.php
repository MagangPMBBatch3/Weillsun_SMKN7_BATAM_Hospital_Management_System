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
                  ->constrained('pembayaran_pasien')
                  ->onDelete('cascade');

            $table->enum('tipe_biaya', [
                'konsultasi', 
                'obat', 
                'lab', 
                'radiologi', 
                'rawat_inap', 
                'lainnya'
            ]);

            $table->unsignedBigInteger('referensi_id')->nullable();

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
