<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiActivity extends Model
{
    protected $fillable = [
        'id',
        'purchase_code',
        'item_id',
        'domain',
        'event_type',
    ];
}
