<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'id',
        'item_id',
        'item_name',
        'purchase_code',
        'purchase_time',
        'buyer',
        'activated_domain',
        'license',
        'user_agent',
        'ip',
        'os',
        'purchase_count',
    ];
}
