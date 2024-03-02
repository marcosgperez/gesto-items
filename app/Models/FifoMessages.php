<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FifoMessages extends Model
{   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'message',
        'conversation_id',
        'phone',
        'instance',
        'send_timestamp',
        'errors'
    ];
}
