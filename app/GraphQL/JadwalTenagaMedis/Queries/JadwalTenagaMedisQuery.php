<?php

namespace App\GraphQL\JadwalTenagaMedis\Queries;

use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;

class JadwalTenagaMedisQuery
{
    public function all($_, array $args)
    {
        $query = JadwalTenagaMedis::query();

        // Filter by tenaga_medis_id if provided (for non-admin users)
        if (!empty($args['tenagaMedisId'])) {
            $query->where('tenaga_medis_id', $args['tenagaMedisId']);
        }

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jam_mulai', 'like', "%$search%")
                    ->orWhere('jam_selesai', 'like', "%$search%");
            })
                ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
                })
                ->orWhereHas('poli', function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%$search%");
                });
        }

        $perPage = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        // Get distinct tenaga_medis_id for pagination counting
        $distinctDoctorsQuery = JadwalTenagaMedis::query();

        if (!empty($args['tenagaMedisId'])) {
            $distinctDoctorsQuery->where('tenaga_medis_id', $args['tenagaMedisId']);
        }

        if (!empty($args['search'])) {
            $search = $args['search'];
            $distinctDoctorsQuery->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jam_mulai', 'like', "%$search%")
                    ->orWhere('jam_selesai', 'like', "%$search%");
            })
                ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
                })
                ->orWhereHas('poli', function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%$search%");
                });
        }

        // Get paginated distinct doctors
        $distinctDoctorsIds = $distinctDoctorsQuery
            ->distinct('tenaga_medis_id')
            ->pluck('tenaga_medis_id')
            ->toArray();

        $totalDoctors = count($distinctDoctorsIds);
        $offset = ($page - 1) * $perPage;
        $pagedDoctorIds = array_slice($distinctDoctorsIds, $offset, $perPage);

        // Get all schedules for the paginated doctors
        $data = JadwalTenagaMedis::query();

        if (!empty($args['tenagaMedisId'])) {
            $data->where('tenaga_medis_id', $args['tenagaMedisId']);
        }

        if (!empty($pagedDoctorIds)) {
            $data->whereIn('tenaga_medis_id', $pagedDoctorIds);
        }

        $schedules = $data->get();

        // Calculate pagination info based on doctors count
        $lastPage = ceil($totalDoctors / $perPage);

        return [
            'data' => $schedules,
            'paginatorInfo' => [
                'hasMorePages' => $page < $lastPage,
                'currentPage' => $page,
                'lastPage' => $lastPage,
                'perPage' => $perPage,
                'total' => $totalDoctors,
            ],
        ];
    }

    public function getJadwalByTenagaMedisAndHari($_, array $args)
    {
        $tenagaMedisId = $args['tenaga_medis_id'] ?? null;
        $hari = $args['hari'] ?? null;

        if (!$tenagaMedisId || $hari === null) {
            return [];
        }

        return JadwalTenagaMedis::where('tenaga_medis_id', $tenagaMedisId)
            ->where('hari', $hari)
            ->get()
            ->toArray();
    }

    //  
}
