<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $table = 'tbl_outlets';
    protected $keyType = 'string'; // karena pakai UUID
    public $incrementing = false;  // non-auto increment

    protected $fillable = [
        'id',
        'name',
        'address',
    ];

    /**
     * Set UUID otomatis saat create.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Relasi ke users.
     * Outlet punya banyak user.
     */
    public function users()
    {
        return $this->hasMany(Users::class, 'outlet_id');
    }

    /**
     * Relasi ke tickets.
     * Outlet punya banyak tiket.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'outlet_id');
    }
}
