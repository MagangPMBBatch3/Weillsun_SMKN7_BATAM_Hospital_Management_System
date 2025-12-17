<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trigger AFTER INSERT
        DB::unprepared("
            CREATE TRIGGER trg_after_insert_detail_pembelian
            AFTER INSERT ON detail_pembelian_obat
            FOR EACH ROW
            BEGIN
                UPDATE pembelian_obat
                SET total_biaya = (
                    SELECT COALESCE(SUM(subtotal), 0)
                    FROM detail_pembelian_obat
                    WHERE pembelian_id = NEW.pembelian_id
                )
                WHERE id = NEW.pembelian_id;
            END;
        ");

        // Trigger AFTER UPDATE
        DB::unprepared("
            CREATE TRIGGER trg_after_update_detail_pembelian
            AFTER UPDATE ON detail_pembelian_obat
            FOR EACH ROW
            BEGIN
                IF OLD.pembelian_id != NEW.pembelian_id THEN
                    UPDATE pembelian_obat
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembelian_obat
                        WHERE pembelian_id = OLD.pembelian_id
                    ), 0)
                    WHERE id = OLD.pembelian_id;

                    UPDATE pembelian_obat
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembelian_obat
                        WHERE pembelian_id = NEW.pembelian_id
                    ), 0)
                    WHERE id = NEW.pembelian_id;
                ELSE
                    UPDATE pembelian_obat
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembelian_obat
                        WHERE pembelian_id = NEW.pembelian_id
                    ), 0)
                    WHERE id = NEW.pembelian_id;
                END IF;
            END;
        ");
        
        // Trigger AFTER DELETE
        DB::unprepared("
            CREATE TRIGGER trg_after_delete_detail_pembelian
            AFTER DELETE ON detail_pembelian_obat
            FOR EACH ROW
            BEGIN
                UPDATE pembelian_obat
                SET total_biaya = (
                    SELECT COALESCE(SUM(subtotal), 0)
                    FROM detail_pembelian_obat
                    WHERE pembelian_id = OLD.pembelian_id
                )
                WHERE id = OLD.pembelian_id;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_insert_detail_pembelian;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_update_detail_pembelian;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_delete_detail_pembelian;");
    }
};
