<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Model\Model\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil attendance yang user-nya memiliki role student
        $user = Auth::user();
        
        $attendances = Attendance::whereHas('user', function($query) {
            $query->role('student'); // Pastikan 'student' adalah role yang sesuai.
        })
        ->orderBy('id', 'DESC')
        ->get();
        

        return view('admin.tables_attend.table_student', [
            'attendances' => $attendances,
        ], compact('user'));
        
    }

    public function indexStudentTeacher()
    {
        // Mengambil attendance yang user-nya memiliki role student
        $user = Auth::user();
        $attendances = Attendance::whereHas('user', function($query) {
            $query->role('student'); // Pastikan 'student' adalah role yang sesuai.
        })
        ->orderBy('id', 'DESC')
        ->get();
        

        return view('teacher.student_attend.index', [
            'attendances' => $attendances,
        ], compact('user'));
        
    }

    public function indexStudentAttend(){
        $user = Auth::user(); // Dapatkan user yang sedang login
    
        // Ambil attendance hanya untuk user yang sedang login
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->get();
    
        return view('student.student_attendance.index', [
            'attendances' => $attendances,
            'user' => $user, // Bisa juga ditambahkan untuk menampilkan info user di view
        ]);
    }
    

    public function scopeRole($query, $roleName)
    {
        return $query->whereHas('roles', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function form_student() {
        return view('student.dashboard.index');
    }


    public function indexListStudent() {
        $user = Auth::user();
        
        $studentRole = Role::where('name', 'student')->first();
        $students = $studentRole->users;
        return view('admin.student_list.index', compact('students', 'user'));
    }

    public function createStudentList(){
        $user = Auth::user();
        return view('admin.student_list.create', compact('user'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi foto
            ]);

            // Handle photo upload
            $path = $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null;

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'photo' => $path, // Simpan path foto
            ]);

            // Assign admin role to user
            $studentRole = Role::where('name', 'student')->first();
            if ($studentRole) {
                $user->assignRole($studentRole);
            }

            // Redirect with success message
            return redirect()->route('dashboard.student_list.index')->with('success', 'Data berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return back()->with('error', 'Email sudah digunakan');
        }
    }

    public function edit($userId)
    {
        $teacher = User::findOrFail($userId);
        $user = Auth::user();

        return view('admin.student_list.edit', compact('teacher', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $userId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $userId,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $teacher = User::findOrFail($userId);

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }

            $path = $request->file('photo')->store('photos', 'public');
            if ($path) {
                $teacher->photo = $path;
                Log::info('Photo updated: ' . $path); // Add logging
            } else {
                Log::error('Failed to store photo'); // Log error if storage fails
                return redirect()->back()->with('error', 'Failed to upload photo.');
            }
        }

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];

        if ($teacher->save()) {
            Log::info('User updated successfully: ' . $teacher->id); // Log successful update
            return redirect()->route('dashboard.student_list.index')->with('success', 'Data berhasil diubah.');
        } else {
            Log::error('Failed to update user: ' . $teacher->id); // Log error if save fails
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    public function resetPassword($userId)
    {
        $user = Auth::user();
        $teacher = User::findOrFail($userId);
        return view('admin.student_list.reset_password', compact('teacher', 'user'));
    }

    public function storePassword(Request $request, $userId)
    {
        $request->validate([
            'password' => 'required|string|min:8', // Validasi password jika diperlukan
        ]);

        $user = User::findOrFail($userId);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('dashboard.student_list.index')->with('success', 'Password berhasil direset');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($userId)
    {
        // Mendapatkan pengguna yang sedang login
        $currentUser = Auth::user();

        // Memeriksa apakah ID pengguna yang ingin dihapus sama dengan ID pengguna yang sedang login
        if ($currentUser->id == $userId) {
            // Redirect atau tampilkan pesan error jika pengguna mencoba menghapus dirinya sendiri
            return redirect()->route('dashboard.student_list.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Jika ID pengguna tidak sama, lanjutkan dengan penghapusan
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('dashboard.student_list.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attendance.create');
    }

   
}
