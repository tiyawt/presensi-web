<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Classroom;
use Illuminate\Http\Request;
use App\Models\Qrcode as QrcodeModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
        $latestQrcode = QrcodeModel::latest()->first(); // Mengambil QR code terakhir

        return view('teacher.qrcode.create', compact('user', 'courses', 'classrooms', 'latestQrcode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required',
        ]);

        // Tentukan path untuk QR code
        $qrCodePath = 'qrcodes/' . uniqid() . '.svg';
        // Pastikan direktori ada
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0755, true);
        }

        DB::beginTransaction();
        try {
            // Simpan data QR Code ke tabel dan ambil entry yang baru dibuat
            $qrcodeEntry = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => $qrCodePath,  // Path sementara
            ]);

            // Setelah entry disimpan, buat $qrData dengan ID QR Code
            $qrData = $qrcodeEntry->id . ' ' . $validatedData['lesson_time'];

            // Generate QR code using QrCode facade dengan $qrData
            $qrcode = new Generator;
            $qrcode->format('svg')
                ->size(300)
                ->generate($qrData, public_path($qrCodePath));

            // Update path setelah QR code dibuat
            $qrcodeEntry->update(['qr_code_path' => $qrCodePath]);

            DB::commit();

            return redirect()->route('dashboard.qrcode.create')
                ->with('success', 'QR Code created successfully')
                ->with('qr_code_path', $qrCodePath)
                ->with('id', $qrData);  // Mengirimkan id dan lesson_time sebagai session
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create QR Code. Please try again.']);
        }
    }



    public function indexTeacherQr()
    {
        $user = Auth::User();
        $courses = Course::all(); // Mengambil semua data dari tabel 'course'
        $classrooms = Classroom::all(); // Mengambil semua data dari tabel 'classroom'
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
        $latestQrcode = QrcodeModel::latest()->first(); // Mengambil QR code terakhir

        return view('admin.attendance.qrcode', compact('user', 'courses', 'classrooms', 'latestQrcode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTeacherQr(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'classroom_id' => 'required|integer',
            'lesson_time' => 'required|date',
        ]);

        // Tentukan path untuk QR code
        $qrCodePath = 'qrcodes/' . uniqid() . '.png';

        // Pastikan direktori ada
        if (!file_exists(public_path('qrcodes'))) {
            mkdir(public_path('qrcodes'), 0755, true);
        }

        DB::beginTransaction();
        try {
            // Simpan data QR Code ke tabel dan ambil entry yang baru dibuat
            $qrcodeEntry = QrcodeModel::create([
                'course_id' => $validatedData['course_id'],
                'classroom_id' => $validatedData['classroom_id'],
                'lesson_time' => $validatedData['lesson_time'],
                'qr_code_path' => $qrCodePath,  // Path sementara
            ]);

            // Setelah entry disimpan, buat $qrData dengan ID QR Code
            $qrData = $qrcodeEntry->id . ' ' . $validatedData['lesson_time'];

            // Generate QR code using QrCode facade dengan $qrData
            $qrcode = new Generator;
            $qrcode->format('png')
                ->size(300)
                ->generate($qrData, public_path($qrCodePath));

            // Update path setelah QR code dibuat
            $qrcodeEntry->update(['qr_code_path' => $qrCodePath]);

            DB::commit();

            return redirect()->route('dashboard.attendance.qrcode')
                ->with('success', 'QR Code created successfully')
                ->with('qr_code_path', $qrCodePath)
                ->with('id', $qrData);  // Mengirimkan id dan lesson_time sebagai session
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QR Code creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create QR Code. Please try again.']);
        }
    }







    /**
     * Display the specified resource.
     */
    public function show(Qrcode $qrcode) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Qrcode $qrcode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Qrcode $qrcode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Qrcode $qrcode)
    {
        //
    }
}
