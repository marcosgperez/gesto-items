<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Checks extends Model
{   
    use SoftDeletes;
    protected $dates = ['deleted_at', 'payment_date'];
    protected $fillable = [
        'image',
        'amount',
        'from',
        'to',
        'status',
        'instance',
        'phone'
    ];
}
