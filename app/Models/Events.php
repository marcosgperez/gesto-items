<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'event_type',
        'start_date',
        'end_date',
        'description',
        'photos',
        'ovserations',
        'history_id',
        'item_id',
    ];

}
