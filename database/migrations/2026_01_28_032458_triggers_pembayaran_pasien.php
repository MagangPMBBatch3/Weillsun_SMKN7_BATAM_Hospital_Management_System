<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
   public function up(): void
    {
        // Trigger AFTER INSERT
        DB::unprepared("
            CREATE TRIGGER after_insert_detail_pembayaran_pasien
            AFTER INSERT ON detail_pembayaran_pasien
            FOR EACH ROW
            BEGIN
                UPDATE pembayaran_pasien
                SET total_biaya = (
                    SELECT COALESCE(SUM(subtotal), 0)
                    FROM detail_pembayaran_pasien
                    WHERE pembayaran_id = NEW.pembayaran_id
                )
                WHERE id = NEW.pembayaran_id;
            END;
        ");

        // Trigger AFTER UPDATE
        DB::unprepared("
            CREATE TRIGGER after_update_detail_pembayaran_pasien
            AFTER UPDATE ON detail_pembayaran_pasien
            FOR EACH ROW
            BEGIN
                IF OLD.pembayaran_id != NEW.pembayaran_id THEN
                    UPDATE pembayaran_pasien
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembayaran_pasien
                        WHERE pembayaran_id = OLD.pembayaran_id
                    ), 0)
                    WHERE id = OLD.pembayaran_id;

                    UPDATE pembayaran_pasien
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembayaran_pasien
                        WHERE pembayaran_id = NEW.pembayaran_id
                    ), 0)
                    WHERE id = NEW.pembayaran_id;
                ELSE
                    UPDATE pembayaran_pasien
                    SET total_biaya = COALESCE((
                        SELECT SUM(subtotal)
                        FROM detail_pembayaran_pasien
                        WHERE pembayaran_id = NEW.pembayaran_id
                    ), 0)
                    WHERE id = NEW.pembayaran_id;
                END IF;
            END;
        ");

        // Trigger AFTER DELETE
        DB::unprepared("
            CREATE TRIGGER after_delete_detail_pembayaran_pasien
            AFTER DELETE ON detail_pembayaran_pasien
            FOR EACH ROW
            BEGIN
                UPDATE pembayaran_pasien
                SET total_biaya = (
                    SELECT COALESCE(SUM(subtotal), 0)
                    FROM detail_pembayaran_pasien
                    WHERE pembayaran_id = OLD.pembayaran_id
                )
                WHERE id = OLD.pembayaran_id;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_insert_detail_pembayaran_pasien;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_update_detail_pembayaran_pasien;");
        DB::unprepared("DROP TRIGGER IF EXISTS after_delete_detail_pembayaran_pasien;");
    }
};
