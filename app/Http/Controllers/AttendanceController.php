<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Qrcode;
use Spatie\Permission\Models\Role;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil total siswa dan guru
        $studentRole = Role::where('name', 'student')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $totalStudents = $studentRole ? $studentRole->users->count() : 0;
        $totalTeachers = $teacherRole ? $teacherRole->users->count() : 0;

        // Define start and end dates for the current week (Monday to Saturday)
        $startDate = now()->startOfWeek()->format('Y-m-d');
        $endDate = now()->startOfWeek()->addDays(5)->format('Y-m-d');

        // Initialize arrays to hold attendance data
        $attendedStudentsPerDay = [];
        $attendedTeachersPerDay = [];

        // Populate data for each day of the week
        foreach (range(0, 5) as $dayOffset) {
            $date = now()->startOfWeek()->addDays($dayOffset)->format('Y-m-d');
            $dayName = now()->startOfWeek()->addDays($dayOffset)->format('l');

            $attendedStudentsPerDay[$dayName] = $studentRole ? $studentRole->users()
                ->whereHas('attendances', function ($query) use ($date) {
                    $query->whereDate('scan_at', $date);
                })->count() : 0;

            $attendedTeachersPerDay[$dayName] = $teacherRole ? $teacherRole->users()
                ->whereHas('attendances', function ($query) use ($date) {
                    $query->whereDate('scan_at', $date);
                })->count() : 0;
        }

        // Ambil siswa dan guru yang hadir
        $attendedStudents = $studentRole ? $studentRole->users()->whereHas('attendances')->count() : 0;
        $attendedTeachers = $teacherRole ? $teacherRole->users()->whereHas('attendances')->count() : 0;

        // Hitung siswa dan guru yang absen
        $absentStudents = $totalStudents - $attendedStudents;
        $absentTeachers = $totalTeachers - $attendedTeachers;

        $user = Auth::user();

        return view('admin.dashboard.index', [
            'user' => $user,
            'totalStudents' => $totalStudents,
            'attendedStudents' => $attendedStudents,
            'absentStudents' => $absentStudents,
            'totalTeachers' => $totalTeachers,
            'attendedTeachers' => $attendedTeachers,
            'absentTeachers' => $absentTeachers,
            'attendedStudentsPerDay' => $attendedStudentsPerDay,
            'attendedTeachersPerDay' => $attendedTeachersPerDay,
        ]);
    }

    public function student_index()
    {
        $user = Auth::user();
        return view('student.dashboard.index', compact('user'));
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage (Student Scan).
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'User tidak terautentikasi'], 401);
            }

            Log::info('Received request data (Student): ' . json_encode($request->all()));

            // Terima 'qr_code' (string hasil scan) atau 'qr_code_id'
            $request->validate([
                'qr_code' => 'nullable|string',
                'qr_code_id' => 'nullable',
            ]);

            $scannedData = $request->input('qr_code') ?? $request->input('qr_code_id');

            if (!$scannedData) {
                return response()->json(['error' => 'Data QR Code tidak ditemukan'], 422);
            }

            $qrCode = Qrcode::find($scannedData);
            if (!$qrCode) {
                return response()->json(['error' => 'QR Code tidak valid atau tidak ditemukan'], 404);
            }

            // Cek apakah siswa sudah melakukan scan untuk QR Code ini hari ini
            $existingAttendance = Attendance::where('qr_code_id', $qrCode->id)
                ->where('user_id', $user->id)
                ->whereDate('scan_at', Carbon::today())
                ->first();

            if ($existingAttendance) {
                Log::warning('Duplicate scan detected for QR code ID ' . $qrCode->id . ' by user ID ' . $user->id);
                return response()->json(['error' => 'Anda sudah melakukan scan QR Code ini hari ini'], 422);
            }

            // Simpan data presensi
            $attendance = new Attendance();
            $attendance->qr_code_id = $qrCode->id;
            $attendance->user_id = $user->id;
            $attendance->scan_at = Carbon::now();
            $attendance->save();

            Log::info('Attendance record created: ' . json_encode($attendance));

            return response()->json(['success' => 'Absensi berhasil dicatat!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in AttendanceController@store: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in AttendanceController@store: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mencatat absensi: ' . $e->getMessage()], 500);
        }
    }

    public function teacher_create_scan()
    {
        $user = Auth::user();
        return view('teacher.teacher_scan.scan', compact('user'));
    }

    /**
     * Store a newly created resource in storage (Teacher Scan).
     */
    public function teacher_scan(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['error' => 'User tidak terautentikasi'], 401);
            }

            Log::info('Received request data (Teacher): ' . json_encode($request->all()));

            // Terima 'qr_code' (string hasil scan) atau 'qr_code_id'
            $request->validate([
                'qr_code' => 'nullable|string',
                'qr_code_id' => 'nullable',
            ]);

            $scannedData = $request->input('qr_code') ?? $request->input('qr_code_id');

            if (!$scannedData) {
                return response()->json(['error' => 'Data QR Code tidak ditemukan'], 422);
            }

            $qrCode = Qrcode::find($scannedData);
            if (!$qrCode) {
                return response()->json(['error' => 'QR Code tidak valid atau tidak ditemukan'], 404);
            }

            // Cek apakah guru sudah melakukan scan untuk QR Code ini hari ini
            $existingAttendance = Attendance::where('qr_code_id', $qrCode->id)
                ->where('user_id', $user->id)
                ->whereDate('scan_at', Carbon::today())
                ->first();

            if ($existingAttendance) {
                Log::warning('Duplicate scan detected for QR code ID ' . $qrCode->id . ' by user ID ' . $user->id);
                return response()->json(['error' => 'Anda sudah melakukan scan QR Code ini hari ini'], 422);
            }

            // Simpan data presensi
            $attendance = new Attendance();
            $attendance->qr_code_id = $qrCode->id;
            $attendance->user_id = $user->id;
            $attendance->scan_at = Carbon::now();
            $attendance->save();

            Log::info('Attendance record created: ' . json_encode($attendance));

            return response()->json(['success' => 'Absensi berhasil dicatat!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in AttendanceController@teacher_scan: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in AttendanceController@teacher_scan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mencatat absensi: ' . $e->getMessage()], 500);
        }
    }

    public function show() {}

    public function edit(Attendance $attendance) {}

    public function update(Request $request, Attendance $attendance) {}

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $user = $attendance->user;

        $attendance->delete();

        if ($user && $user->hasRole('teacher')) {
            return redirect()->route('dashboard.tables_attend.table_teacher')->with('success', 'Attendance record deleted successfully.');
        } else {
            return redirect()->route('dashboard.tables_attend.table_student')->with('success', 'Attendance record deleted successfully.');
        }
    }
}
