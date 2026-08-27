<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;



class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $classrooms = Classroom::get();
        return view('admin.classroom.index', compact('classrooms', 'user'));
    }

    public function take_classroom()
    {
        $user = Auth::user();
        $courses = Course::all(); // Mengambil semua data dari tabel 'course'
        $classrooms = Classroom::all(); // Mengambil semua data dari tabel 'classroom'
        return view('teacher.dashboard.index', [
            'courses' => $courses,
            'classrooms' => $classrooms
        ], compact('user'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        return view('admin.classroom.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'classroom_name' => ['required', 'string', 'max:255'],
            ]);

            $existingClassroom = Classroom::whereRaw('LOWER(name) = ?', [strtolower($data['classroom_name'])])
                ->whereNull('deleted_at')
                ->first();

            if ($existingClassroom) {
                return back()->with('error', 'Nama kelas sudah ada. Pilih nama yang berbeda.');
            }

            Classroom::create([
                'name' => $data['classroom_name'],
                'slug' => Str::slug($data['classroom_name'])
            ]);

            return redirect()->route('dashboard.classroom.index')->with('success', 'Kelas berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return back()->with('error', $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $classroom = Classroom::findOrFail($id);
        $user = Auth::user();
        return view('admin.classroom.edit', compact('classroom', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $classroom = Classroom::findOrFail($id);
            // Validate input
            $data = $request->validate([
                'classroom_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('classrooms', 'name')
                        ->ignore($classroom->id)
                        ->whereNull('deleted_at') // Ignores soft-deleted records
                ],
            ]);

            // Find the course
            $classroom = Classroom::findOrFail($id);

            // Update the course with name and slug
            $classroom->update([
                'name' => $data['classroom_name'],
                'slug' => Str::slug($data['classroom_name'])
            ]);

            return redirect()->route('dashboard.classroom.index')->with('success', 'Kelas berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui kelas.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $classroom = Classroom::findOrFail($id);

        // Cek apakah ada jadwal yang menggunakan course ini
        if ($classroom->schedules()->exists()) {
            return redirect()->route('dashboard.classroom.index')->with('error', 'Kelas tidak bisa dihapus karena masih digunakan di jadwal.');
        }

        // Jika tidak digunakan, lanjutkan penghapusan
        $classroom->delete();
        return redirect()->route('dashboard.classroom.index')->with('success', 'Kelas berhasil dihapus');
    }
}
