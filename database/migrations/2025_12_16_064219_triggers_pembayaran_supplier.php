<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Trigger AFTER UPDATE on pembelian_obat to update pembayaran_supplier
        // DB::unprepared("
        //     CREATE TRIGGER trg_after_update_pembelian_obat
        //     AFTER UPDATE ON pembelian_obat
        //     FOR EACH ROW
        //     BEGIN
        //         IF OLD.total_biaya != NEW.total_biaya THEN
        //             UPDATE pembayaran_supplier
        //             SET jumlah_bayar = NEW.total_biaya
        //             WHERE pembelian_id = NEW.id;
        //         END IF;
        //     END;
        // ");

        // INSERT pembayaran → status lunas
        DB::unprepared("
            CREATE TRIGGER trg_after_insert_pembayaran_supplier
            AFTER INSERT ON pembayaran_supplier
            FOR EACH ROW
            BEGIN
                UPDATE pembelian_obat
                SET status = 'lunas'
                WHERE id = NEW.pembelian_id;
            END;
        ");

        // DELETE pembayaran → status pending
        DB::unprepared("
            CREATE TRIGGER trg_after_delete_pembayaran_supplier
            AFTER DELETE ON pembayaran_supplier
            FOR EACH ROW
            BEGIN
                UPDATE pembelian_obat
                SET status = 'pending'
                WHERE id = OLD.pembelian_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_update_pembelian_obat");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_insert_pembayaran_supplier");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_after_delete_pembayaran_supplier");
    }
};
