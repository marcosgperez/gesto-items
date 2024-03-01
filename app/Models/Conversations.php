<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Conversations extends Model
{   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'chat_open_timestamp',
        'chat_close_timestamp',
        'bot_mode',
        'client_id'
    ];
}
