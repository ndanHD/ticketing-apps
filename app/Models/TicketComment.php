<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketComment extends Model
{
    protected $table = 'tbl_ticket_comments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'ticket_id', 'user_id', 'comment', 'is_rating_comment'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
