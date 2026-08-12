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
        // 1. Validasi input
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // 2. Simpan entry awal ke database
            $qrcodeEntry = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => '', // Placeholder sementara
            ]);

            // 3. Buat isi string data QR Code
            $qrData = $qrcodeEntry->id . ' ' . $validatedData['lesson_time'];

            // 4. Generate QR Code langsung di memory sebagai Data URI SVG (Tanpa menulis file ke public_path)
            $qrcode = new Generator;
            $qrSvg = $qrcode->format('svg')->size(300)->generate($qrData);
            $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

            // 5. Update kolom qr_code_path dengan Data URI
            $qrcodeEntry->update(['qr_code_path' => $qrCodeDataUri]);

            DB::commit();

            // 6. Redirect ke halaman penampil QR Code
            return redirect()->route('dashboard.attendance.qrcode')
                ->with('success', 'QR Code berhasil dibuat!')
                ->with('qr_code_path', $qrCodeDataUri)
                ->with('id', $qrData);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());

            // Mengembalikan pesan error asli agar mudah untuk debugging
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
        $user = Auth::User();
        $courses = Course::all();
        $classrooms = Classroom::all();
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
                ->with('success', 'QR Code berhasil dibuat!')
                ->with('qr_code_path', $qrCodeDataUri)
                ->with('id', $qrData);

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