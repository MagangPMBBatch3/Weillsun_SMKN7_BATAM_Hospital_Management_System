<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        
        DB::unprepared("
            CREATE TRIGGER trg_resep_obat_after_insert
            AFTER INSERT ON resep_obat
            FOR EACH ROW
            BEGIN
                UPDATE obat
                SET stok = stok - NEW.jumlah
                WHERE id = NEW.obat_id;
            END
        ");

        
        DB::unprepared("
            CREATE TRIGGER trg_resep_obat_after_delete
            AFTER DELETE ON resep_obat
            FOR EACH ROW
            BEGIN
                UPDATE obat
                SET stok = stok + OLD.jumlah
                WHERE id = OLD.obat_id;
            END
        ");

       
        DB::unprepared("
            CREATE TRIGGER trg_resep_obat_after_update
            AFTER UPDATE ON resep_obat
            FOR EACH ROW
            BEGIN
                -- Jika obat tidak berubah
                IF OLD.obat_id = NEW.obat_id THEN
                    UPDATE obat
                    SET stok = stok + OLD.jumlah - NEW.jumlah
                    WHERE id = NEW.obat_id;
                ELSE
                    -- Kembalikan stok obat lama
                    UPDATE obat
                    SET stok = stok + OLD.jumlah
                    WHERE id = OLD.obat_id;

                    -- Kurangi stok obat baru
                    UPDATE obat
                    SET stok = stok - NEW.jumlah
                    WHERE id = NEW.obat_id;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_resep_obat_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_resep_obat_after_delete");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_resep_obat_after_update");
    }
};
