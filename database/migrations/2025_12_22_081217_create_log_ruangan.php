<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * =========================
         * TABEL LOG RUANGAN
         * =========================
         */
        Schema::create('log_ruangan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ruangan_id')->constrained('ruangan');
            $table->foreignId('rawat_inap_id')->constrained('rawat_inap');
            $table->unsignedBigInteger('pasien_id');

            $table->enum('status_sebelum', ['tersedia', 'tidak_tersedia']);
            $table->enum('status_sesudah', ['tersedia', 'tidak_tersedia']);

            $table->enum('aksi', [
                'MASUK',
                'PULANG',
                'PINDAH_MASUK',
                'PINDAH_KELUAR',
                'RESTORE'
            ]);

            $table->timestamp('waktu')->useCurrent();
        });

        /**
         * =========================
         * TRIGGER MASUK RAWAT INAP
         * =========================
         */
        // DB::unprepared("
        //     CREATE TRIGGER after_insert_rawat_inap
        //     AFTER INSERT ON rawat_inap
        //     FOR EACH ROW
        //     BEGIN
        //         INSERT INTO log_ruangan (
        //             ruangan_id,
        //             rawat_inap_id,
        //             pasien_id,
        //             status_sebelum,
        //             status_sesudah,
        //             aksi,
        //             waktu
        //         ) VALUES (
        //             NEW.ruangan_id,
        //             NEW.id,
        //             NEW.pasien_id,
        //             'tersedia',
        //             'tidak_tersedia',
        //             'MASUK',
        //             NOW()
        //         );

        //         UPDATE ruangan
        //         SET status = 'tidak_tersedia'
        //         WHERE id = NEW.ruangan_id;
        //     END
        // ");

        /**
         * =========================
         * TRIGGER after (aktif)
         * =========================
         */
        // DB::unprepared("
        //     CREATE TRIGGER after_update_rawat_inap_aktif
        //     AFTER UPDATE ON rawat_inap
        //     FOR EACH ROW
        //     BEGIN
        //         IF NEW.status = 'Aktif' THEN

        //             INSERT INTO log_ruangan (
        //                 ruangan_id,
        //                 rawat_inap_id,
        //                 pasien_id,
        //                 status_sebelum,
        //                 status_sesudah,
        //                 aksi,
        //                 waktu
        //             ) VALUES (
        //                 OLD.ruangan_id,
        //                 OLD.id,
        //                 OLD.pasien_id,
        //                 'tersedia',
        //                 'tidak_tersedia',
        //                 'MASUK',
        //                 NOW()
        //             );

        //             UPDATE ruangan
        //             SET status = 'tidak_tersedia'
        //             WHERE id = OLD.ruangan_id;

        //         END IF;
        //     END
        // ");

        /**
         * =========================
         * TRIGGER PULANG
         * =========================
         */
        DB::unprepared("
            CREATE TRIGGER after_update_rawat_inap_pulang
            AFTER UPDATE ON rawat_inap
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'Pulang' THEN

                    INSERT INTO log_ruangan (
                        ruangan_id,
                        rawat_inap_id,
                        pasien_id,
                        status_sebelum,
                        status_sesudah,
                        aksi,
                        waktu
                    ) VALUES (
                        OLD.ruangan_id,
                        OLD.id,
                        OLD.pasien_id,
                        'tidak_tersedia',
                        'tersedia',
                        'PULANG',
                        NOW()
                    );

                    UPDATE ruangan
                    SET status = 'tersedia'
                    WHERE id = OLD.ruangan_id;

                END IF;
            END
        ");

        /**
         * =========================
         * TRIGGER PINDAH RUANGAN
         * =========================
         */
        DB::unprepared("
            CREATE TRIGGER after_update_rawat_inap_pindah
            AFTER UPDATE ON rawat_inap
            FOR EACH ROW
            BEGIN
                IF OLD.ruangan_id <> NEW.ruangan_id THEN

                    -- LOG KELUAR RUANGAN LAMA
                    INSERT INTO log_ruangan (
                        ruangan_id,
                        rawat_inap_id,
                        pasien_id,
                        status_sebelum,
                        status_sesudah,
                        aksi,
                        waktu
                    ) VALUES (
                        OLD.ruangan_id,
                        OLD.id,
                        OLD.pasien_id,
                        'tidak_tersedia',
                        'tersedia',
                        'PINDAH_KELUAR',
                        NOW()
                    );

                    -- LOG MASUK RUANGAN BARU
                    INSERT INTO log_ruangan (
                        ruangan_id,
                        rawat_inap_id,
                        pasien_id,
                        status_sebelum,
                        status_sesudah,
                        aksi,
                        waktu
                    ) VALUES (
                        NEW.ruangan_id,
                        OLD.id,
                        OLD.pasien_id,
                        'tersedia',
                        'tidak_tersedia',
                        'PINDAH_MASUK',
                        NOW()
                    );

                    UPDATE ruangan SET status = 'tersedia' WHERE id = OLD.ruangan_id;
                    UPDATE ruangan SET status = 'tidak_tersedia' WHERE id = NEW.ruangan_id;

                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_insert_rawat_inap");
        DB::unprepared("DROP TRIGGER IF EXISTS after_update_rawat_inap_pulang");
        DB::unprepared("DROP TRIGGER IF EXISTS after_update_rawat_inap_pindah");

        Schema::dropIfExists('log_ruangan');
    }
};
