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
        'photos',
        'brand',
        'code',
        'serial',
        'model',
        'description',
        'item_id'
    ];
}
