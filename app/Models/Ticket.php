<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tbl_tickets';

    protected $fillable = [
        'ticket_type_id',
        'sla_id',
        'assign_to',
        'created_by',
        'status',
        'detail'
    ];

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function sla()
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function ratings()
    {
        return $this->hasMany(TicketRating::class, 'ticket_id');
    }
}
