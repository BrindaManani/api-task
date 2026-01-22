<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequest extends Model
{
    protected $fillable = [
        'id',
        'purchase_code',
        'domain',
        'endpoint',
        'method',
    ];
}
