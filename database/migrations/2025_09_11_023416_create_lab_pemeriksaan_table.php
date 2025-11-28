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
        Schema::create('lab_pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->foreignId('tenaga_medis_id')->constrained('tenaga_medis')->cascadeOnDelete();
            $table->string('jenis_pemeriksaan');
            $table->text('hasil')->nullable();
            $table->date('tanggal');
            $table->decimal('biaya_lab', 15, 2)->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_pemeriksaan');
    }
};
