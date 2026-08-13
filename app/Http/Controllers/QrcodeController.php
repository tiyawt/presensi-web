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

        return view('teacher.qrcode.create', compact('user', 'courses', 'classrooms', 'latestQrcode'));
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

            // 2. Generate SVG Base64 Data URI
            $qrcode = new Generator;
            $qrSvg = $qrcode->format('svg')->size(300)->generate($qrData);
            $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

            // 3. Simpan Data URI LANGSUNG ke Database
            $qrcodeEntry->update(['qr_code_path' => $qrCodeDataUri]);

            DB::commit();

            // 4. REDIRECT BERSIH (HANYA KIRIM PESAN SINGKAT, TANPA BASE64)
            return redirect()->route('dashboard.qrcode.create')
                ->with('success', 'QR Code berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat QR Code: ' . $e->getMessage()]);
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

        // Ambil QR Code paling akhir yang baru saja dibuat
        $latestQrcode = QrcodeModel::with(['course', 'classroom'])->latest()->first();

        return view('admin.attendance.qrcode', compact('user', 'courses', 'classrooms', 'latestQrcode'));
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

            // Generate SVG Data URI tanpa disk write
            $qrcode = new Generator;
            $qrSvg = $qrcode->format('svg')->size(300)->generate($qrData);
            $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

            $qrcodeEntry->update(['qr_code_path' => $qrCodeDataUri]);

            DB::commit();

            return redirect()->route('dashboard.attendance.qrcode')
                ->with('success', 'QR Code berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat QR Code: ' . $e->getMessage()]);
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
