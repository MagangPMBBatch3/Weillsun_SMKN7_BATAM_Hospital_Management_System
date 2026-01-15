<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pasien\Pasien;
use App\Models\Kunjungan\Kunjungan;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Support\Facades\Auth;
use App\Models\Obat\Obat;
use App\Models\Poli\Poli;
use App\Models\Ruangan\Ruangan;
use App\Models\Supplier\Supplier;
use App\Models\RawatInap\RawatInap;
use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;
use App\Models\KunjunganUlang\KunjunganUlang;
use App\Models\PembayaranPasien\PembayaranPasien;

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
        $firstDayMonth = Carbon::now()->startOfMonth();

        // Get doctor profile
        $doctorProfile = $user->profile->tenagaMedis ?? null;

        // My Appointments Today
        $myAppointmentsToday = 0;
        $myScheduleToday = [];
        $myPatientsToday = [];
        $labTestsPending = 0;
        $radiologyPending = 0;
        $totalMyPatients = 0;
        $myPatientsMonth = 0;
        $avgConsultation = 0;
        $pendingRecords = 0;
        $pendingPrescriptions = 0;

        if ($doctorProfile) {
            // Get schedule for today
            $dayName = $today->format('l');
            $schedules = JadwalTenagaMedis::where('tenaga_medis_id', $doctorProfile->id)
                ->where('hari', $dayName)
                ->with('poli')
                ->get();

            $myScheduleToday = $schedules->map(function ($schedule) {
                return [
                    'clinic' => $schedule->poli->nama_poli ?? 'Clinic',
                    'start_time' => $schedule->jam_mulai,
                    'end_time' => $schedule->jam_selesai,
                ];
            })->toArray();

            // Get visits for today
            $visits = Kunjungan::with(['pasien', 'poli'])
                ->whereDate('tanggal_kunjungan', $today)
                ->whereIn('poli_id', $schedules->pluck('poli_id'))
                ->get();

            $myAppointmentsToday = $visits->count();

            $myPatientsToday = $visits->map(function ($visit) {
                return [
                    'name' => $visit->pasien->nama ?? 'Unknown',
                    'time' => $visit->tanggal_kunjungan->format('H:i'),
                ];
            })->toArray();

            // Total my patients
            $totalMyPatients = Kunjungan::whereIn('poli_id', $schedules->pluck('poli_id'))
                ->distinct('pasien_id')
                ->count();

            $myPatientsMonth = Kunjungan::whereDate('tanggal_kunjungan', '>=', $firstDayMonth)
                ->whereIn('poli_id', $schedules->pluck('poli_id'))
                ->distinct('pasien_id')
                ->count();
        }


        return view('dashboard', [
            'myAppointmentsToday' => $myAppointmentsToday,
            'myScheduleToday' => $myScheduleToday,
            'myPatientsToday' => $myPatientsToday,
            'pendingRecords' => $pendingRecords,
            'pendingPrescriptions' => $pendingPrescriptions,
            'labTestsPending' => $labTestsPending,
            'radiologyPending' => $radiologyPending,
            'totalMyPatients' => $totalMyPatients,
            'myPatientsMonth' => $myPatientsMonth,
            'avgConsultation' => $avgConsultation,
        ]);
    }

    private function cashierDashboard()
    {
        $today = Carbon::today();
        $firstDayMonth = Carbon::now()->startOfMonth();

        try {
            // Today's Revenue - menggunakan field yang benar
            $todayRevenue = PembayaranPasien::whereDate('tanggal_bayar', $today)
                ->sum('total_biaya') ?? 0;

            // Monthly Revenue
            $monthlyRevenue = PembayaranPasien::whereDate('tanggal_bayar', '>=', $firstDayMonth)
                ->sum('total_biaya') ?? 0;

            // Pending Payments
            $pendingPayments = PembayaranPasien::count();

            $pendingAmount = PembayaranPasien::sum('total_biaya') ?? 0;

            // Total Transactions
            $totalTransactions = PembayaranPasien::whereDate('tanggal_bayar', $today)
                ->count();

            // Outstanding Balance
            $outstandingBalance = PembayaranPasien::sum('total_biaya') ?? 0;

            // Recent Transactions - menggunakan relasi 'detail' bukan 'detailPembayaran'
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
                ->limit(5)
                ->get()
                ->map(function ($payment) {
                    return [
                        'patient_name' => $payment->pasien->nama ?? 'Unknown',
                        'amount' => 'Rp ' . number_format($payment->total_biaya ?? 0, 0, ',', '.'),
                        'time' => $payment->created_at->format('H:i'),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            // Fallback values
            $todayRevenue = 0;
            $monthlyRevenue = 0;
            $pendingPayments = 0;
            $pendingAmount = 0;
            $totalTransactions = 0;
            $outstandingBalance = 0;
            $recentTransactions = [];
            $cashPayment = 0;
            $transferPayment = 0;
            $pendingVerification = [];
        }

        return view('dashboard', [
            'todayRevenue' => $todayRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'pendingPayments' => $pendingPayments,
            'pendingAmount' => $pendingAmount,
            'totalTransactions' => $totalTransactions,
            'outstandingBalance' => $outstandingBalance,
            'recentTransactions' => $recentTransactions,
            'cashPayment' => $cashPayment,
            'transferPayment' => $transferPayment,
            'pendingVerification' => $pendingVerification,
        ]);
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
            ->limit(10)
            ->get()
            ->map(function ($appointment) {
                return [
                    'patient_name' => $appointment->pasien->nama ?? 'Unknown',
                    'poli_name' => $appointment->poli->nama_poli ?? 'Unknown',
                    'time' => $appointment->jam_ulang->format('H:i'),
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
        $completedAppointments = Kunjungan::whereDate('tanggal_kunjungan', $today)
            ->whereNotNull('created_at')
            ->count();

        $inProgressAppointments = Kunjungan::whereDate('tanggal_kunjungan', $today)
            ->whereNull('created_at')
            ->count();

        $scheduledAppointments = Kunjungan::whereDate('tanggal_kunjungan', '>', $today)->count();
        $cancelledAppointments = 0;



        return view('dashboard', [
            'todayAppointments' => $todayAppointments,
            'newPatientsToday' => $newPatientsToday,
            'doctorsOnDutyCount' => $doctorsOnDutyCount,
            'receptDoctorsOnDuty' => $receptDoctorsOnDuty,
            'upcomingAppointments' => $upcomingAppointments,
            'clinicQueue' => $clinicQueue,
            'newPatients' => $newPatients,
            'completedAppointments' => $completedAppointments,
            'inProgressAppointments' => $inProgressAppointments,
            'scheduledAppointments' => $scheduledAppointments,
            'cancelledAppointments' => $cancelledAppointments,
        ]);
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
