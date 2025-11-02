<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shift extends Model
{
    protected $table = 'tbl_shift';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'name', 'start_time', 'end_time'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function handlerShiftSchedules()
    {
        return $this->hasMany(HandlerShiftSchedule::class, 'shift_id');
    }
}
