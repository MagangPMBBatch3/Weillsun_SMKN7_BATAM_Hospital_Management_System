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
        Schema::create('libur_tenaga_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenaga_medis_id')->constrained('tenaga_medis')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('jenis', ['libur', 'sakit', 'izin'])->default('libur');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libur_tenaga_medis');
    }
};
