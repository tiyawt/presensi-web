<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EachScheduleController;
use App\Http\Controllers\UserScheduleController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//test git

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
    
        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin.index');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('dashboard.teacher.index');
        } else {
            return redirect()->route('dashboard.student.index');
        }
    }
    
    
    return view('dashboard'); // Default view jika tidak ada role
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('dashboard')->name('dashboard.')->group(function(){

        // Form Admin
        Route::get('admin/index', [AttendanceController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.index');

        //Untuk menambahkan jadwal pelajaran
        Route::get('/schedule/create', [UserScheduleController::class, 'create'])
        ->middleware('role:admin')
        ->name('schedule.create');

        Route::post('schedule/create', [UserScheduleController::class, 'store'])
            ->middleware('role:admin')
            ->name('schedule.store');

        //Untuk mengedit jadwal pelajaran
        // Route untuk menampilkan form edit
        Route::get('/dashboard/schedule/{id}/edit', [UserScheduleController::class, 'edit'])
        ->middleware('role:admin')
        ->name('schedule.edit');

        // Route untuk memperbarui data
        Route::put('/dashboard/schedule/{id}', [UserScheduleController::class, 'update'])
        ->middleware('role:admin')
        ->name('schedule.update');

        //Untuk menambahkan jadwal pelajaran
        Route::get('user/schedule/create', [EachScheduleController::class, 'create'])
        ->middleware('role:admin')
        ->name('each_schedule.create');

        Route::post('/user/schedule/create', [EachScheduleController::class, 'store'])
            ->middleware('role:admin')
            ->name('each_schedule.store');

        //Untuk mengedit jadwal pelajaran
        // Route untuk menampilkan form edit
        Route::get('/dashboard/user/schedule/{id}/edit', [EachScheduleController::class, 'edit'])
        ->middleware('role:admin')
        ->name('each_schedule.edit');

        //Menghapus Jadwal pelajaran
        Route::delete('/user/schedule/{id}', [EachScheduleController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('each_schedule.delete');

        // Route untuk memperbarui data
        Route::put('/dashboard/user/schedule/{id}', [EachScheduleController::class, 'update'])
        ->middleware('role:admin')
        ->name('each_schedule.update');

         // Route untuk menampilkan form edit
         Route::get('/dashboard/course/edit/{id}', [CourseController::class, 'edit'])
         ->middleware('role:admin')
         ->name('course.edit');
 
         // Route untuk memperbarui data
         Route::put('/dashboard/course/{id}', [CourseController::class, 'update'])
         ->middleware('role:admin')
         ->name('course.update');

         // Route untuk menampilkan form edit
         Route::get('/dashboard/classroom/edit/{id}', [ClassroomController::class, 'edit'])
         ->middleware('role:admin')
         ->name('classroom.edit');
 
         // Route untuk memperbarui data
         Route::put('/dashboard/classroom/{id}', [ClassroomController::class, 'update'])
         ->middleware('role:admin')
         ->name('classroom.update');
        
        //Menghapus Jadwal pelajaran
        Route::delete('/dashboard/schedule/{id}', [UserScheduleController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('schedule.delete');

        //Menghapus course
        Route::delete('/dashboard/course/{id}', [CourseController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('course.delete');

        //Menghapus classroom
        Route::delete('/dashboard/classroom/{id}', [ClassroomController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('classroom.delete');
        
        //Form Teacher
        //Untuk menampilkan seluruh courses oleh teacher
        // Route::get('teacher/index', [CourseController::class, 'take_course'])
        // ->middleware('role:teacher')
        // ->name('teacher.index');

        Route::get('teacher/index', [ClassroomController::class, 'take_classroom'])
        ->middleware('role:teacher')
        ->name('teacher.index');

        Route::get('/teacher/scan', [AttendanceController::class, 'teacher_create_scan'])
        ->middleware('role:teacher')
        ->name('teacher_scan.scan');

        Route::post('/teacher/scan-qr', [AttendanceController::class, 'teacher_scan'])
        ->middleware('auth','role:teacher')
        ->name('teacher.store');


        // Guru create qr code
        Route::get('student/qrcode/create/', [QRCodeController::class, 'create'])
            ->middleware('role:teacher')
            ->name('qrcode.create');
            
        Route::post('/student/qrcode', [QRCodeController::class, 'store'])
            ->middleware('role:teacher')
            ->name('qrcode.store');

        
        
        


        // Form Student Role
        Route::post('/scan-qr', [AttendanceController::class, 'store'])
        ->middleware('auth', 'role:student')
        ->name('student.store');

        Route::get('/student/index', [AttendanceController::class, 'student_index'])
        ->middleware('role:student')
        ->name('student.index');

        //Untuk menampilkan seluruh jadwal 
        Route::get('/schedule/show', [UserScheduleController::class, 'index'])
        ->middleware('role:admin')
        ->name('schedule.index');

        //Untuk menampilkan seluruh jadwal 
        Route::get('user/schedule/show', [EachScheduleController::class, 'index'])
        ->middleware('role:admin')
        ->name('each_schedule.index');

        // untuk menampilkan jadwal guru itu sendiri
        Route::get('/teacher/schedule/show', [EachScheduleController::class, 'indexTeacherSchedule'])
        ->middleware('role:teacher')
        ->name('teacher_schedule.index');

        // untuk menampilkan jadwal guru itu sendiri
        Route::get('/student/schedule/show', [EachScheduleController::class, 'indexStudentSchedule'])
        ->middleware('role:student')
        ->name('student_schedule.index');



        //Untuk menampilkan seluruh pelajaran
        Route::get('/course/show', [CourseController::class, 'index'])
        ->middleware('role:admin')
        ->name('course.index');

        //Untuk menampilkan seluruh kelas
        Route::get('/classroom/show', [ClassroomController::class, 'index'])
        ->middleware('role:admin')
        ->name('classroom.index');

        //Untuk menampilkan seluruh kehadiran teacher
        Route::get('/attendance/show/teachers', [TeacherController::class, 'index'])
        ->middleware('role:admin')
        ->name('tables_attend.table_teacher');

        //Untuk menampilkan seluruh kehadiran student
        Route::get('/attendance/show/students', [StudentController::class, 'index'])
        ->middleware('role:admin')
        ->name('tables_attend.table_student');

        //Untuk menampilkan seluruh kehadiran student disisi teacher
        Route::get('/student_attend/show/students', [StudentController::class, 'indexStudentTeacher'])
        ->middleware('role:teacher')
        ->name('student_attend.index');

        //Untuk menampilkan seluruh kehadiran student disisi student itu sendiri
        Route::get('/student/show', [StudentController::class, 'indexStudentAttend'])
        ->middleware('role:student')
        ->name('student_attendance.index');

        //Untuk menampilkan seluruh kehadiran teacher disisi teacher itu sendiri
        Route::get('/teacher/show', [TeacherController::class, 'indexTeacherAttend'])
        ->middleware('role:teacher')
        ->name('teacher_attend.index');


        //Untuk menampilkan seluruh data admin
        Route::get('/admin_list/show/index', [AdminController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin_list.index');

        //untuk menambahkan admin baru
        
        Route::get('/admin/create', [AdminController::class, 'create'])
        ->middleware('role:admin')
        ->name('admin.create');

        //Untuk save admin baru
        // Route::post('/admin/create', [AdminController::class, 'store'])
        // ->middleware('role:admin')
        // ->name('admin.store');

        Route::get('/admin_list/create', [AdminController::class, 'create'])
        ->middleware('role:admin')
        ->name('admin_list.create');

        //Untuk save admin baru
        Route::post('/admin_list/create', [AdminController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin_list.store');

        Route::get('/admin_list/reset-password/{id}', [AdminController::class, 'resetPassword'])
        ->middleware('role:admin')
        ->name('admin_list.reset_password');

        Route::post('/admin_list/store-password/{id}', [AdminController::class, 'storePassword'])
        ->middleware('role:admin')
        ->name('admin_list.storePassword');

        Route::post('/admin_list/create', [AdminController::class, 'store'])
        ->middleware('role:admin')
        ->name('admin_list.store');

        Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])
        ->middleware('role:admin')
        ->name('admin_list.edit');

        //Untuk save teacher list

        Route::get('/teacher_list/create', [TeacherController::class, 'createTeacherList'])
        ->middleware('role:admin')
        ->name('teacher_list.create');

        Route::get('/teacher_list/show/index', [TeacherController::class, 'indexListTeacher'])
        ->middleware('role:admin')
        ->name('teacher_list.index');

        Route::post('/teacher_list/create', [TeacherController::class, 'store'])
        ->middleware('role:admin')
        ->name('teacher_list.store');

        Route::get('/teacher_list/reset-password/{id}', [TeacherController::class, 'resetPassword'])
        ->middleware('role:admin')
        ->name('teacher_list.reset_password');

        Route::post('/teacher_list/store-password/{id}', [TeacherController::class, 'storePassword'])
        ->middleware('role:admin')
        ->name('teacher_list.storePassword');

        Route::post('/teacher_list/create', [TeacherController::class, 'store'])
        ->middleware('role:admin')
        ->name('teacher_list.store');

        Route::get('/teacher/edit/{id}', [TeacherController::class, 'edit'])
        ->middleware('role:admin')
        ->name('teacher_list.edit');

        // Route untuk memperbarui data teacher
        Route::put('/teacher/edit/{id}', [TeacherController::class, 'update'])
        ->middleware('role:admin')
        ->name('teacher_list.update');

       Route::delete('/teacher/delete/{id}', [TeacherController::class, 'destroy'])
       ->middleware('role:admin')
       ->name('teacher_list.delete');

       //Untuk save student list

       Route::get('/student_list/create', [StudentController::class, 'createStudentList'])
       ->middleware('role:admin')
       ->name('student_list.create');

       Route::get('/student_list/show/index', [StudentController::class, 'indexListStudent'])
       ->middleware('role:admin')
       ->name('student_list.index');

       Route::post('/student_list/create', [StudentController::class, 'store'])
       ->middleware('role:admin')
       ->name('student_list.store');

       Route::get('/student_list/reset-password/{id}', [StudentController::class, 'resetPassword'])
       ->middleware('role:admin')
       ->name('student_list.reset_password');

       Route::post('/student_list/store-password/{id}', [StudentController::class, 'storePassword'])
       ->middleware('role:admin')
       ->name('student_list.storePassword');

       Route::post('/student_list/create', [StudentController::class, 'store'])
       ->middleware('role:admin')
       ->name('student_list.store');

       Route::get('/student/edit/{id}', [StudentController::class, 'edit'])
       ->middleware('role:admin')
       ->name('student_list.edit');

       // Route untuk memperbarui data teacher
       Route::put('/student/edit/{id}', [StudentController::class, 'update'])
       ->middleware('role:admin')
       ->name('student_list.update');

      Route::delete('/student/delete/{id}', [StudentController::class, 'destroy'])
      ->middleware('role:admin')
      ->name('student_list.delete');


         // Route untuk memperbarui data
         Route::put('/admin/edit/{id}', [AdminController::class, 'update'])
         ->middleware('role:admin')
         ->name('admin_list.update');

        Route::delete('/admin/delete/{id}', [AdminController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('admin_list.delete');


        //Buat Qr code kahadiran guru
        Route::get('teacher/attendance/create', [QrcodeController::class, 'indexTeacherQr'])
        ->middleware('role:admin')
        ->name('attendance.create');

        Route::get('teacher/qrcode/create/', [QRCodeController::class, 'createTeacherQr'])
            ->middleware('role:admin')
            ->name('attendance.qrcode');

        Route::post('/teacher/qrcode', [QRCodeController::class, 'storeTeacherQr'])
            ->middleware('role:admin')
            ->name('attendance.store');

        
        
        //Menghapus Attendances
        Route::delete('/dashboard/attendance/{id}', [AttendanceController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('attendance.delete');


        //Untuk create qr code kehadiran students 
        Route::post('/qrcode/students', [QrcodeController::class, 'qrcode_generate'])
        ->middleware(['role:teacher', 'role:admin'])
        ->name('dashboard.create');



        //Untuk create qr code kehadiran teacher 
        Route::get('/qrcode/teachers/{teacher}', [QrcodeController::class, 'qrcode_students'])
        ->middleware('role:admin')
        ->name('qrcode.teachers.create');

        
        

        //Untuk create course
        Route::get('/course/create', [CourseController::class, 'create'])
        ->middleware('role:admin')
        ->name('course.create');


        //Untuk save course
        Route::post('/course/create', [CourseController::class, 'store'])
        ->middleware('role:admin')
        ->name('course.store');

        

        //Untuk create classroom
        Route::get('/classroom/create', [ClassroomController::class, 'create'])
        ->middleware('role:admin')
        ->name('classroom.create');

        //Untuk save classroom
        Route::post('/classroom/create', [ClassroomController::class, 'store'])
        ->middleware('role:admin')
        ->name('classroom.store');

        
    });


});

require __DIR__.'/auth.php';