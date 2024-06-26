<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'qr',
        'photos',
        'brand',
        'code',
        'serial',
        'model',
        'chasis',
        'description',
        'manual',
        'sector_id',
        'floor_id',
        'history_id',
        'status',
        'phones_to_remind',
        'text_to_send',
        'reminder_interval',
        'last_reminder',
        'client_id',
        'location_id'
    ];
}
