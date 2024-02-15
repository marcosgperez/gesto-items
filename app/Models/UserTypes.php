<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class UserTypes extends Model
{   
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name'
    ];

    
}
