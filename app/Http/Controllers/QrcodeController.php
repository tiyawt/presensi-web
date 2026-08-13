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

        $createdId = session('created_qrcode_id');

        if ($createdId) {
            $latestQrcode = QrcodeModel::with(['course', 'classroom'])->find($createdId);
        } else {
            $latestQrcode = QrcodeModel::with(['course', 'classroom'])->orderBy('id', 'desc')->first();
        }

        $qrCodeImage = null;
        if ($latestQrcode && $latestQrcode->qr_code_path) {
            $qrSvg = (new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(300)->generate($latestQrcode->qr_code_path);
            $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        }

        return view('teacher.qrcode.create', compact('user', 'courses', 'classrooms', 'latestQrcode', 'qrCodeImage'));
    }

    /**
     * Store a newly created resource in storage (Teacher).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required',
        ]);

        try {
            $qrData = $validatedData['classroom_id'] . '-' . $validatedData['course_id'] . '-' . time() . ' ' . $validatedData['lesson_time'];

            $qrcode = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => $qrData,
            ]);

            return redirect()->route('dashboard.qrcode.create')
                ->with('success', 'QR Code berhasil dibuat!')
                ->with('created_qrcode_id', $qrcode->id);
        } catch (\Throwable $e) {
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

        // 1. Cek apakah ada ID QR yang baru saja dibuat dari session
        $createdId = session('created_qrcode_id');

        if ($createdId) {
            $latestQrcode = QrcodeModel::with(['course', 'classroom'])->find($createdId);
        } else {
            // Fallback: Urutkan secara eksplisit berdasarkan ID terbesar (bukan timestamp latest())
            $latestQrcode = QrcodeModel::with(['course', 'classroom'])->orderBy('id', 'desc')->first();
        }

        $qrCodeImage = null;
        if ($latestQrcode && $latestQrcode->qr_code_path) {
            $qrSvg = (new \SimpleSoftwareIO\QrCode\Generator)->format('svg')->size(300)->generate($latestQrcode->qr_code_path);
            $qrCodeImage = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        }

        return view('admin.attendance.qrcode', compact('user', 'courses', 'classrooms', 'latestQrcode', 'qrCodeImage'));
    }

    /**
     * Store a newly created resource in storage (Admin/Teacher QR).
     */
    public function storeTeacherQr(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required',
        ]);

        try {
            $qrData = $validatedData['classroom_id'] . '-' . $validatedData['course_id'] . '-' . time() . ' ' . $validatedData['lesson_time'];

            // Simpan ke variabel agar kita bisa mengambil ID-nya
            $qrcode = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => $qrData,
            ]);

            // Kirim 'created_qrcode_id' ke session redirect
            return redirect()->route('dashboard.attendance.qrcode')
                ->with('success', 'QR Code berhasil dibuat!')
                ->with('created_qrcode_id', $qrcode->id);
        } catch (\Throwable $e) {
            Log::error('Admin QR creation failed: ' . $e->getMessage());

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
