<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'tbl_shift';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    public function handlerSchedules()
    {
        return $this->hasMany(HandlerShiftSchedule::class, 'shift_id');
    }
}
