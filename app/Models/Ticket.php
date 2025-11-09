<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $table = 'tbl_tickets';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ticket_type_id',
        'sla_id',
        'assign_to',
        'created_by',
        'status',
        'title',
        'description',
        'priority',
        'deleted_by'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function sla()
    {
        return $this->belongsTo(SLA::class, 'sla_id');
    }

    public function assignTo()
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

    public function commentRatings()
    {
        return $this->hasMany(TicketCommentRating::class, 'ticket_id');
    }

    /**
     * Relasi ke user yang menghapus tiket
     */
    public function deletedByUser()
    {
        return $this->belongsTo(Users::class, 'deleted_by');
    }
}
