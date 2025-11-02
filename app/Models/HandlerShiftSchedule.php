<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandlerShiftSchedule extends Model
{
    use HasFactory;

    protected $table = 'tbl_handler_shift_schedules';

    protected $fillable = [
        'user_id',
        'shift_id',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
