<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'role_id',
        'name',
        'jabatan',
        'outlet',
        'email',
        'password',
        'must_change_password',
        'last_change_password',
        'reset_token',
        'is_active'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Periksa apakah user memiliki role tertentu
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function handlerShiftSchedules()
    {
        return $this->hasMany(HandlerShiftSchedule::class, 'user_id');
    }

    public function ticketsAssigned()
    {
        return $this->hasMany(Ticket::class, 'assign_to');
    }

    public function ticketsCreated()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function ticketComments()
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    public function ticketRatingsGiven()
    {
        return $this->hasMany(TicketRating::class, 'user_id');
    }

    public function ticketRatingsReceived()
    {
        return $this->hasMany(TicketRating::class, 'target_user_id');
    }
}
