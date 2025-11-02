<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket_types';

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_type_id');
    }
}
