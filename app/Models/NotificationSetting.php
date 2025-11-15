<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;
    protected $table = 'tbl_notification_settings';
    protected $fillable = ['whatsapp', 'email', 'desktop'];
}
