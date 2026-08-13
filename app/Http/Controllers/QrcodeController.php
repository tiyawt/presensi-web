<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Generator;

class QrcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ('this is qrcode.com');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $courses = Course::all();
        $classrooms = Classroom::all();

        // Mengambil QR code terakhir beserta relasi course & classroom
        $latestQrcode = QrcodeModel::with(['course', 'classroom'])->latest()->first();

        // Generate gambar QR SAAT DITAMPILKAN saja (tidak disimpan ke DB),
        // dari data pendek yang tersimpan di qr_code_path (mis. "5 2026-08-13T10:11").
        $qrCodeImage = null;
        if ($latestQrcode && $latestQrcode->qr_code_path) {
            $qrSvg = (new Generator)->format('svg')->size(300)->generate($latestQrcode->qr_code_path);
            $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        }

        return view('teacher.qrcode.create', compact('user', 'courses', 'classrooms', 'latestQrcode', 'qrCodeImage'));
    }

    /**
     * Store a newly created resource in storage (Teacher).
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat entry di database
            $qrcodeEntry = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => '',
            ]);

            $qrData = $qrcodeEntry->id . ' ' . $validatedData['lesson_time'];

            // 2. Simpan data PENDEK saja ke database (bukan base64 SVG).
            //    Gambar QR-nya di-generate ulang tiap kali halaman ditampilkan
            //    (lihat method create()/createTeacherQr()), supaya tidak pernah
            //    menyimpan/mengirim blob besar ke DB Turso maupun ke session.
            $qrcodeEntry->update(['qr_code_path' => $qrData]);

            DB::commit();

            // 3. REDIRECT BERSIH (HANYA KIRIM PESAN SINGKAT)
            // Redirect ke halaman create milik teacher sendiri (role:teacher),
            // BUKAN ke 'dashboard.attendance.qrcode' yang di-guard role:admin.
            return redirect()->route('dashboard.qrcode.create')
                ->with('success', 'QR Code berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());

            // Batasi panjang pesan sebelum di-flash ke session, supaya
            // cookie session tidak membengkak (>4096 byte) walau pesan
            // exception aslinya sangat panjang (misalnya berisi query/data).
            $shortMessage = \Illuminate\Support\Str::limit($e->getMessage(), 300);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat QR Code: ' . $shortMessage]);
        }
    }

    public function indexTeacherQr()
    {
        $user = Auth::User();
        $courses = Course::all();
        $classrooms = Classroom::all();

        return view('admin.attendance.create', [
            'courses' => $courses,
            'classrooms' => $classrooms
        ], compact('user'));
    }

    public function createTeacherQr()
{
    $user = Auth::user();
    $courses = Course::all();
    $classrooms = Classroom::all();

    // Ambil QR Code paling akhir
    $latestQrcode = QrcodeModel::with(['course', 'classroom'])->latest()->first();

    // Generate gambar QR jika data qr_code_path ada
    $qrCodeImage = null;
    if ($latestQrcode && $latestQrcode->qr_code_path) {
        $qrSvg = (new Generator)->format('svg')->size(300)->generate($latestQrcode->qr_code_path);
        $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
    }

    return view('admin.attendance.qrcode', compact('user', 'courses', 'classrooms', 'latestQrcode', 'qrCodeImage'));
}

    /**
     * Store a newly created resource in storage (Admin/Teacher QR).
     */
    public function storeTeacherQr(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'course_id' => 'required|integer',
        'classroom_id' => 'required|integer',
        'lesson_time' => 'required',
    ]);

    DB::beginTransaction();
    try {
        $qrcodeEntry = QrcodeModel::create([
            'course_id' => $validatedData['course_id'],
            'classroom_id' => $validatedData['classroom_id'],
            'lesson_time' => $validatedData['lesson_time'],
            'qr_code_path' => '',
        ]);

        $qrData = $qrcodeEntry->id . ' ' . $validatedData['lesson_time'];

        // Simpan HANYA teks pendek ke DB, bukan data:image/svg+xml;base64...
        $qrcodeEntry->update(['qr_code_path' => $qrData]);

        DB::commit();

        return redirect()->route('dashboard.attendance.qrcode')
            ->with('success', 'QR Code berhasil dibuat!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('QR Code creation failed: ' . $e->getMessage());

        $shortMessage = \Illuminate\Support\Str::limit($e->getMessage(), 300);

        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Gagal membuat QR Code: ' . $shortMessage]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(QrcodeModel $qrcode) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QrcodeModel $qrcode) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QrcodeModel $qrcode) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QrcodeModel $qrcode) {}
}
