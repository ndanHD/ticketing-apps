<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    use HasFactory;

    protected $table = 'tbl_slas';

    protected $fillable = [
        'name',
        'duration'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sla_id');
    }
}
