<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $table = 'tbl_roles';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'name', 'can_handle_ticket'];

    public function users()
    {
        return $this->hasMany(Users::class, 'role_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }
}
