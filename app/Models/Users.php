<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_users';

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'must_change_password',
        'last_change_password',
        'reset_token',
        'is_active',
    ];

    protected $hidden = ['password', 'reset_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function shiftSchedules()
    {
        return $this->hasMany(HandlerShiftSchedule::class, 'user_id');
    }

    public function ticketsCreated()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function ticketsAssigned()
    {
        return $this->hasMany(Ticket::class, 'assign_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(TicketRating::class, 'user_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(TicketRating::class, 'target_user_id');
    }
}
