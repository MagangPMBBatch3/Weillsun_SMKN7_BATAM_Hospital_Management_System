<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Obat\Obat;
use App\Models\Poli\Poli;
use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use App\Models\Supplier\Supplier;
use App\Models\Kunjungan\Kunjungan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use Illuminate\Support\Facades\Auth;
use App\Models\RekamMedis\RekamMedis;
use App\Models\TenagaMedis\TenagaMedis;
use App\Models\KunjunganUlang\KunjunganUlang;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\PembayaranPasien\PembayaranPasien;
use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;
use App\Models\LiburTenagaMedis\LiburTenagaMedis;
use App\Models\ResepObat\ResepObat;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            return $this->adminDashboard();
        } elseif ($role === 'doctor') {
            return $this->doctorDashboard();
        } elseif ($role === 'cashier') {
            return $this->cashierDashboard();
        } elseif ($role === 'receptionist') {
            return $this->receptionistDashboard();
        }

        return view('dashboard');
    }

    private function adminDashboard()
    {
        $today = Carbon::today();
        $firstDayMonth = Carbon::now()->startOfMonth();


        // Total Patients
        $totalPatients = Pasien::count();
        $newPatientsMonth = Pasien::whereDate('created_at', '>=', $firstDayMonth)->count();

        // Total Medical Staff
        $totalMedicalStaff = TenagaMedis::count();

        // Today's Visits
        $todayVisits = Kunjungan::whereDate('tanggal_kunjungan', $today)->count();
        $monthlyVisits = Kunjungan::whereDate('tanggal_kunjungan', '>=', $firstDayMonth)->count();

        // Revenue - menggunakan field yang benar
        $todayRevenue = PembayaranPasien::whereDate('tanggal_bayar', $today)->sum('total_biaya') ?? 0;
        $monthlyRevenue = PembayaranPasien::whereDate('tanggal_bayar', '>=', $firstDayMonth)->sum('total_biaya') ?? 0;

        // Inpatient
        $inpatientCount = RawatInap::whereNull('tanggal_keluar')->count();

        // Pending Payments - menggunakan field yang benar
        $pendingPaymentsCount = PembayaranPasien::count() ?? 0;
        $pendingPaymentsAmount = PembayaranPasien::sum('total_biaya') ?? 0;

        // Medicine
        $medicineTypes = Obat::count();
        $lowStockAlert = Obat::where('stok', '<', 10)->count();

        // Clinics
        $clinicCount = Poli::count();
        $dayName = $today->isoWeekday();
        $activeClinicsToday = JadwalTenagaMedis::where('hari', $dayName)->distinct('poli_id')->count();

        // Doctors on Duty
        $doctorsOnDuty = $this->getDoctorsOnDutyToday();
        $doctorsOnDutyCount = count($doctorsOnDuty);

        // Recent Visits
        $recentVisits = Kunjungan::with(['pasien', 'poli'])
            ->whereDate('tanggal_kunjungan', $today)
            ->latest('tanggal_kunjungan')
            ->limit(10)
            ->get()
            ->map(function ($visit) {
                return [
                    'patient_name' => $visit->pasien->nama ?? 'Unknown',
                    'clinic' => $visit->poli->nama_poli ?? 'General Clinic',
                    'date' => $visit->tanggal_kunjungan->format('d M Y'),
                ];
            })
            ->toArray();

        // Top Clinics
        $topClinics = Kunjungan::whereDate('tanggal_kunjungan', $today)
            ->selectRaw('poli_id, COUNT(*) as visits')
            ->groupBy('poli_id')
            ->orderByDesc('visits')
            ->limit(5)
            ->with('poli')
            ->get()
            ->map(function ($clinic) {
                return [
                    'name' => $clinic->poli->nama_poli ?? 'Clinic',
                    'visits' => $clinic->visits,
                ];
            })
            ->toArray();

        // Low Stock Medicines
        $lowStockMedicines = Obat::where('stok', '<', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($medicine) {
                return [
                    'name' => $medicine->nama_obat ?? 'Medicine',
                    'stock' => $medicine->stok ?? 0,
                ];
            })
            ->toArray();

        // System Stats
        $activeUsers = Kunjungan::whereDate('tanggal_kunjungan', $today)->distinct('pasien_id')->count();
        $totalRooms = Ruangan::count();
        $totalSuppliers = Supplier::count();

        return view('dashboard', compact(
            'totalPatients',
            'newPatientsMonth',
            'totalMedicalStaff',
            'todayVisits',
            'monthlyVisits',
            'todayRevenue',
            'monthlyRevenue',
            'inpatientCount',
            'pendingPaymentsCount',
            'pendingPaymentsAmount',
            'medicineTypes',
            'lowStockAlert',
            'clinicCount',
            'activeClinicsToday',
            'doctorsOnDuty',
            'doctorsOnDutyCount',
            'recentVisits',
            'topClinics',
            'lowStockMedicines',
            'activeUsers',
            'totalRooms',
            'totalSuppliers'
        ));
    }

    private function doctorDashboard()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;
        $firstDayMonth = Carbon::now()->startOfMonth();

        // ================== DOCTOR ==================
        $doctor = TenagaMedis::with([
            'profile.user',
            'jadwal.poli',
            'libur'
        ])
            ->where('profile_id', $user->profile->id)
            ->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Data tenaga medis tidak ditemukan');
        }

        // ================== TODAY SCHEDULE ==================
        $todaySchedule = $doctor->jadwal
            ->where('hari', $dayOfWeek)
            ->first();

        $isOffToday = $doctor->libur
            ->where('tanggal', $today->toDateString())
            ->first();

        $isoDay = $todaySchedule ? $today->isoFormat('dddd') : null;

        // ================== DOCTOR INFO ==================
        $doctorInfo = [
            'name' => $doctor->profile->user->name ?? 'Unknown',
            'specialization' => $doctor->spesialisasi ?? 'General',
            'no_str' => $doctor->no_str ?? 'N/A',
            'photo' => $doctor->profile->foto
                ? asset('storage/' . $doctor->profile->foto)
                : asset('default_pp.jpg'),
        ];

        // ================== SCHEDULE INFO ==================
        if ($isOffToday) {
            $scheduleInfo = [
                'status' => 'off',
                'jenis' => $isOffToday->jenis ?? 'N/A',
                'note' => $isOffToday->keterangan ?? '-',
            ];
        } elseif ($todaySchedule) {
            $scheduleInfo = [
                'status' => 'active',
                'day' => $isoDay,
                'poli_id' => $todaySchedule->poli->id ?? null,
                'poli_name' => $todaySchedule->poli->nama_poli ?? 'General Clinic',
                'start_time' => Carbon::parse($todaySchedule->jam_mulai)->format('H:i'),
                'end_time' => Carbon::parse($todaySchedule->jam_selesai)->format('H:i'),
            ];
        } else {
            $scheduleInfo = [
                'status' => 'no_schedule',
                'day' => $today->format('l'),
                'message' => 'No schedule today',
            ];
        }

        // ================== STATISTICS (OPTIMIZED) ==================
        $stats = [
            'total_patients' => RekamMedis::where('tenaga_medis_id', $doctor->id)
                ->distinct('pasien_id')
                ->count('pasien_id'),

            'today_records' => RekamMedis::where('tenaga_medis_id', $doctor->id)
                ->whereDate('tanggal', $today)
                ->count(),

            'today_followups' => KunjunganUlang::where('tenaga_medis_id', $doctor->id)
                ->whereDate('tanggal_ulang', $today)
                ->count(),

            'pending_labs' => LabPemeriksaan::where('tenaga_medis_id', $doctor->id)
                ->whereNull('hasil')
                ->count(),

            'pending_radiology' => Radiologi::where('tenaga_medis_id', $doctor->id)
                ->whereNull('hasil')
                ->count(),

            'unpaid_prescriptions' => ResepObat::where('tenaga_medis_id', $doctor->id)
                ->where('is_paid', 0)
                ->count(),
        ];

        // ================== TODAY FOLLOW UPS (N+1 SAFE) ==================
        $todayFollowUps = KunjunganUlang::with([
            'kunjungan:id,pasien_id,keluhan',
            'kunjungan.pasien:id,nama',
            'poli:id,nama_poli'
        ])
            ->where('tenaga_medis_id', $doctor->id)
            ->whereDate('tanggal_ulang', $today)
            ->orderBy('jam_ulang')
            ->get()
            ->each(function ($f) {
                $f->jam_ulang_format = $f->jam_ulang
                    ? Carbon::parse($f->jam_ulang)->format('H:i')
                    : '-';

                $f->pasien_nama = $f->kunjungan->pasien->nama ?? 'Unknown';
                $f->pasien_id = $f->kunjungan->pasien->id ?? null;
                $f->nama_poli = $f->poli->nama_poli ?? 'General';
                $f->keluhan = $f->kunjungan->keluhan ?? '-';
            });

        // ================== RECENT ACTIVITIES (LIGHT) ==================
        $recentActivities = collect()

            ->merge(
                RekamMedis::with('pasien:id,nama')
                    ->where('tenaga_medis_id', $doctor->id)
                    ->latest()
                    ->limit(3)
                    ->get()
                    ->map(function ($r) {
                        return (object) [
                            'created_at' => $r->created_at,
                            'pasien_nama' => $r->pasien->nama ?? 'Unknown',
                            'detail' => $r->diagnosis,
                            'type' => 'medical_record',
                        ];
                    })
            )

            ->merge(
                ResepObat::with('pasien:id,nama')
                    ->where('tenaga_medis_id', $doctor->id)
                    ->latest()
                    ->limit(3)
                    ->get()
                    ->map(function ($r) {
                        return (object) [
                            'created_at' => $r->created_at,
                            'pasien_nama' => $r->pasien->nama ?? 'Unknown',
                            'detail' => 'Resep Obat',
                            'type' => 'prescription',
                        ];
                    })
            )

            ->sortByDesc('created_at')
            ->take(6);


        // ================== MY INPATIENTS ==================
        $myInpatients = RawatInap::with([
            'pasien:id,nama',
            'ruangan:id,nama_ruangan'
        ])
            ->where('status', 'Aktif')
            ->whereIn(
                'pasien_id',
                RekamMedis::where('tenaga_medis_id', $doctor->id)
                    ->distinct()
                    ->pluck('pasien_id')
            )
            ->latest('tanggal_masuk')
            ->limit(5)
            ->get()
            ->each(function ($i) {

                // FIX 1: hari rawat inap (integer & manusiawi)
                $i->days_admitted = max(
                    1,
                    Carbon::parse($i->tanggal_masuk)
                        ->startOfDay()
                        ->diffInDays(Carbon::today())
                );

                // FIX 2: properti yang dipakai Blade
                $i->pasien_nama = $i->pasien->nama ?? 'Unknown';
                $i->nama_ruangan = $i->ruangan->nama_ruangan ?? 'Unknown Room';
            });


        // ================== VISIT CHART ==================
        $visitChart = collect(range(6, 0))
            ->map(function ($i) use ($doctor) {
                $date = Carbon::today()->subDays($i);
                return [
                    'date' => $date->format('D, M j'),
                    'count' => RekamMedis::where('tenaga_medis_id', $doctor->id)
                        ->whereDate('tanggal', $date)
                        ->count(),
                ];
            });

        // ================== TOP DIAGNOSES ==================
        $topDiagnoses = RekamMedis::where('tenaga_medis_id', $doctor->id)
            ->whereDate('tanggal', '>=', $firstDayMonth)
            ->selectRaw('diagnosis, COUNT(*) as total')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'doctorInfo',
            'scheduleInfo',
            'stats',
            'todayFollowUps',
            'recentActivities',
            'myInpatients',
            'visitChart',
            'topDiagnoses'
        ));
    }


    private function cashierDashboard()
    {
        $today = Carbon::today();
        $firstDayMonth = Carbon::now()->startOfMonth();

        // Today's Revenue - menggunakan field yang benar
        $todayRevenue = PembayaranPasien::whereDate('tanggal_bayar', $today)
            ->sum('total_biaya') ?? 0;

        // Monthly Revenue
        $monthlyRevenue = PembayaranPasien::whereDate('tanggal_bayar', '>=', $firstDayMonth)
            ->sum('total_biaya') ?? 0;

        // Pending Payments
        $patientPayments = PembayaranPasien::count();

        $patientAmount = PembayaranPasien::sum('total_biaya') ?? 0;

        // Total Transactions
        $totalTransactions = PembayaranPasien::whereDate('tanggal_bayar', $today)
            ->count();

        // Outstanding Balance
        $outstandingBalance = PembayaranPasien::sum('total_biaya') ?? 0;

        // Recent Transactions 
        $recentTransactions = PembayaranPasien::with(['pasien', 'detail'])
            ->whereDate('tanggal_bayar', $today)
            ->latest('tanggal_bayar')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'patient_name' => $payment->pasien->nama ?? 'Unknown',
                    'amount' => $payment->total_biaya ?? 0,
                    'status' => 'Completed',
                    'time' => $payment->created_at->format('H:i'),
                ];
            })
            ->toArray();

        // Payment Methods - menggunakan field yang benar
        $cashPayment = PembayaranPasien::whereDate('tanggal_bayar', $today)
            ->where('metode_bayar', 'cash')
            ->sum('total_biaya') ?? 0;

        $transferPayment = PembayaranPasien::whereDate('tanggal_bayar', $today)
            ->where('metode_bayar', 'transfer')
            ->sum('total_biaya') ?? 0;

        // Pending Verification
        $pendingVerification = PembayaranPasien::with(['pasien'])
            ->limit(6)
            ->get()
            ->map(function ($payment) {
                return [
                    'patient_name' => $payment->pasien->nama ?? 'Unknown',
                    'amount' => 'Rp ' . number_format($payment->total_biaya ?? 0, 0, ',', '.'),
                    'time' => $payment->created_at->format('H:i'),
                ];
            })
            ->toArray();

        return view('dashboard', compact(
            'todayRevenue',
            'monthlyRevenue',
            'patientPayments',
            'patientAmount',
            'totalTransactions',
            'outstandingBalance',
            'recentTransactions',
            'cashPayment',
            'transferPayment',
            'pendingVerification'
        ));
    }

    private function receptionistDashboard()
    {
        $today = Carbon::today();

        // Today's Appointments
        $todayAppointments = Kunjungan::whereDate('tanggal_kunjungan', $today)
            ->count();

        // New Patients Today
        $newPatientsToday = Pasien::whereDate('created_at', $today)
            ->count();

        // Doctors on Duty Today
        $doctorsOnDutyCount = count($this->getDoctorsOnDutyToday());
        $receptDoctorsOnDuty = $this->getDoctorsOnDutyToday();



        // Upcoming Appointments
        $upcomingAppointments = KunjunganUlang::with(['kunjungan.pasien', 'kunjungan.poli'])
            ->whereDate('tanggal_ulang', $today)
            ->orderBy('tanggal_ulang', 'asc')
            ->orderBy('jam_ulang', 'asc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'patient_name' => $appointment->kunjungan->pasien->nama ?? 'Unknown',
                    'poli_name' => $appointment->poli->nama_poli ?? 'Unknown',
                    'doctor_name' => $appointment->tenagaMedis?->profile?->nickname ?? 'Unknown',
                    'time' => Carbon::parse($appointment->jam_ulang)->format('H:i'),
                ];
            })
            ->toArray();

        // Clinic Queue
        $clinicQueue = Poli::withCount(['kunjungan' => function ($q) {
            $q->whereDate('tanggal_kunjungan', Carbon::today());
        }])
            ->get()
            ->map(function ($clinic) {
                return [
                    'name' => $clinic->nama_poli,
                    'queue' => $clinic->kunjungan_count,
                ];
            })
            ->toArray();

        // New Patient Registrations
        $newPatients = Pasien::whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($patient) {
                return [
                    'name' => $patient->nama ?? 'Unknown',
                    'time' => $patient->created_at->format('H:i'),
                ];
            })
            ->toArray();

        // Appointment Status
        $completedAppointments = KunjunganUlang::whereDate('tanggal_ulang', $today)
            ->where('status', 'completed')
            ->count();

        $scheduledAppointments = KunjunganUlang::whereDate('tanggal_ulang', $today)
            ->where('status', 'scheduled')
            ->count();

        $cancelledAppointments = KunjunganUlang::whereDate('tanggal_ulang', $today)
            ->where('status', 'cancelled')
            ->count();



        return view('dashboard', compact(
            'todayAppointments',
            'newPatientsToday',
            'doctorsOnDutyCount',
            'receptDoctorsOnDuty',
            'upcomingAppointments',
            'clinicQueue',
            'newPatients',
            'completedAppointments',
            'scheduledAppointments',
            'cancelledAppointments',
        ));
    }

    private function getDoctorsOnDutyToday(): array
    {
        $today = Carbon::today();
        $day   = $today->isoWeekday();

        return JadwalTenagaMedis::query()
            ->where('hari', $day)
            ->whereHas('tenagaMedis', function ($q) use ($today) {
                $q->whereDoesntHave('libur', function ($l) use ($today) {
                    $l->whereDate('tanggal', $today);
                });
            })
            ->with(['tenagaMedis.profile.user', 'poli'])
            ->get()
            ->unique('tenaga_medis_id')
            ->map(fn($s) => [
                'id' => $s->tenagaMedis->id,
                'name' => $s->tenagaMedis->profile->user->name ?? 'Unknown',
                'specialization' => $s->tenagaMedis->spesialisasi ?? 'General',
                'clinic' => $s->poli->nama_poli ?? 'Clinic',
            ])
            ->values()
            ->toArray();
    }
}
