<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'tbl_notifications';

    protected $fillable = ['id', 'user_id', 'title', 'message', 'type', 'ticket_id', 'is_read'];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
}
