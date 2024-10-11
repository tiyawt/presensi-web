<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['qr_code_id', 'user_schedule_id', 'user_id'];

    public function qrcode()
    {
        return $this->belongsTo(Qrcode::class, 'qr_code_id');
    }

    public function userSchedule()
    {
        return $this->belongsTo(UserSchedule::class, 'user_schedule_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
