<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketRating extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket_ratings';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'target_user_id',
        'rating'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
