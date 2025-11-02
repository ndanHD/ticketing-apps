<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketType extends Model
{
    protected $table = 'tbl_ticket_types';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'name', 'description', 'is_active'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_type_id');
    }
}
