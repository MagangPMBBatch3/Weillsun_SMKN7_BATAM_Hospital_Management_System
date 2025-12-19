<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE TRIGGER after_insert_detail_pembelian_obat
            AFTER INSERT ON detail_pembelian_obat
            FOR EACH ROW
            BEGIN
                DECLARE stok_lama INT;
                DECLARE stok_baru INT;

                -- ambil stok lama
                SELECT stok INTO stok_lama
                FROM obat
                WHERE id = NEW.obat_id
                LIMIT 1;

                -- hitung stok baru
                SET stok_baru = stok_lama + NEW.jumlah;

                -- update stok obat
                UPDATE obat
                SET stok = stok_baru
                WHERE id = NEW.obat_id;

                -- insert log stok
                INSERT INTO log_stok_obat (
                    obat_id,
                    jenis,
                    jumlah,
                    stok_sebelum,
                    stok_sesudah,
                    referensi_id,
                    created_at
                ) VALUES (
                    NEW.obat_id,
                    'MASUK',
                    NEW.jumlah,
                    stok_lama,
                    stok_baru,
                    NEW.pembelian_id,
                    NOW()
                );
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_insert_detail_pembelian_obat
        ");
    }
};
