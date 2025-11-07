<?php

namespace App\Models;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tbl_users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role_id',
        'name',
        'email',
        'jabatan',
        'outlet',
        'password',
        'must_change_password',
        'last_change_password',
        'reset_token',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Menghasilkan token JWT untuk user ini
     */
    public function getJWTToken()
    {
        $payload = [
            'sub' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->name,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // Token berlaku 24 jam
        ];

        return JWT::encode($payload, config('jwt.secret'), 'HS256');
    }

    /**
     * Periksa apakah user memiliki role tertentu
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Relasi ke model Role (role user)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
