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
         $totalStudents = $studentRole->users->count();
         $totalTeachers = $teacherRole->users->count();
     
         // Define start and end dates for the current week (Monday to Saturday)
         $startDate = now()->startOfWeek()->format('Y-m-d');
         $endDate = now()->startOfWeek()->addDays(5)->format('Y-m-d'); // Adding 5 days to Monday to get Saturday
     
         // Initialize arrays to hold attendance data
         $attendedStudentsPerDay = [];
         $attendedTeachersPerDay = [];
     
         // Populate data for each day of the week
         foreach (range(0, 5) as $dayOffset) {
             $date = now()->startOfWeek()->addDays($dayOffset)->format('Y-m-d');
             $dayName = now()->startOfWeek()->addDays($dayOffset)->format('l'); // Get day name
     
             // Fetch attended students and teachers for the specific date
             $attendedStudentsPerDay[$dayName] = $studentRole->users()
                 ->whereHas('attendances', function ($query) use ($date) {
                     $query->whereDate('scan_at', $date);
                 })->count();
     
             $attendedTeachersPerDay[$dayName] = $teacherRole->users()
                 ->whereHas('attendances', function ($query) use ($date) {
                     $query->whereDate('scan_at', $date);
                 })->count();
         }
     
         // Ambil siswa dan guru yang hadir
         $attendedStudents = $studentRole->users()
             ->whereHas('attendances') // Mengambil siswa yang memiliki data absensi
             ->count();
     
         $attendedTeachers = $teacherRole->users()
             ->whereHas('attendances') // Mengambil guru yang memiliki data absensi
             ->count();
     
         // Hitung siswa dan guru yang absen
         $absentStudents = $totalStudents - $attendedStudents;
         $absentTeachers = $totalTeachers - $attendedTeachers;
     
         // Ambil data pengguna yang sedang login
         $user = Auth::user();
     
         // Kembalikan view dengan data
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        {
            try {
                $user = Auth::user();
        
                if (!$user) {
                    return response()->json(['error' => 'User not authenticated'], 401);
                }
        
                Log::info('Received request data: ' . json_encode($request->all()));
        
                $validatedData = $request->validate([
                    'qr_code_id' => 'required|integer|exists:qrcodes,id',
                ]);
        
                $qrCode = Qrcode::findOrFail($validatedData['qr_code_id']);
        
                // Check if attendance for this specific QR code already exists for the user today
                $existingAttendance = Attendance::where('qr_code_id', $qrCode->id)
                                                ->where('user_id', $user->id)
                                                ->whereDate('scan_at', Carbon::today()) // Check by date
                                                ->first();
        
                if ($existingAttendance) {
                    Log::warning('Duplicate scan detected for QR code ID ' . $qrCode->id . ' by user ID ' . $user->id . ' for today');
                    return response()->json(['error' => 'You have already scanned this QR code today'], 422);
                }
        
                // Create a new attendance record if no duplicate found
                $attendance = new Attendance();
                $attendance->qr_code_id = $qrCode->id;
                $attendance->user_id = $user->id;
                $attendance->scan_at = Carbon::now();
                $attendance->save();
        
                Log::info('Attendance record created: ' . json_encode($attendance));
        
                return response()->json(['success' => 'Attendance recorded successfully'], 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Validation error in AttendanceController@teacher_scan: ' . json_encode($e->errors()));
                return response()->json(['error' => $e->errors()], 422);
            } catch (\Exception $e) {
                Log::error('Error in AttendanceController@teacher_scan: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to record attendance: ' . $e->getMessage()], 500);
            }
        }
    }
    
    
    public function teacher_create_scan() {
        $user = Auth::user();
        return view('teacher.teacher_scan.scan', compact('user'));
    }

    public function teacher_scan(Request $request)
    {
        try {
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
    
            Log::info('Received request data: ' . json_encode($request->all()));
    
            $validatedData = $request->validate([
                'qr_code_id' => 'required|integer|exists:qrcodes,id',
            ]);
    
            $qrCode = Qrcode::findOrFail($validatedData['qr_code_id']);
    
            // Check if attendance for this specific QR code already exists for the user today
            $existingAttendance = Attendance::where('qr_code_id', $qrCode->id)
                                            ->where('user_id', $user->id)
                                            ->whereDate('scan_at', Carbon::today()) // Check by date
                                            ->first();
    
            if ($existingAttendance) {
                Log::warning('Duplicate scan detected for QR code ID ' . $qrCode->id . ' by user ID ' . $user->id . ' for today');
                return response()->json(['error' => 'You have already scanned this QR code today'], 422);
            }
    
            // Create a new attendance record if no duplicate found
            $attendance = new Attendance();
            $attendance->qr_code_id = $qrCode->id;
            $attendance->user_id = $user->id;
            $attendance->scan_at = Carbon::now();
            $attendance->save();
    
            Log::info('Attendance record created: ' . json_encode($attendance));
    
            return response()->json(['success' => 'Attendance recorded successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in AttendanceController@teacher_scan: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in AttendanceController@teacher_scan: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to record attendance: ' . $e->getMessage()], 500);
        }
    }
    


    
    
    
    
    
    


    
    


    /**
     * Display the specified resource.
     */
    public function show(){}

    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    // Temukan record attendance yang akan dihapus
    $attendance = Attendance::findOrFail($id);
    
    // Ambil user yang terkait dengan attendance
    $user = $attendance->user;

    // Hapus record attendance
    $attendance->delete();

    // Tentukan URL pengalihan berdasarkan jenis pengguna
    if ($user->hasRole('teacher')) {
        // Jika pengguna adalah guru, arahkan ke halaman guru
        return redirect()->route('dashboard.tables_attend.table_teacher')->with('success', 'Attendance record deleted successfully.');
    } else {
        // Jika pengguna adalah murid, arahkan ke halaman murid
        return redirect()->route('dashboard.tables_attend.table_student')->with('success', 'Attendance record deleted successfully.');
    }
}


    
}