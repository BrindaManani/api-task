<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResetLicenseActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'id',
        'purchase_code',
        'reset_license_time',
    ];
    protected $casts = [
        'reset_license_time' => 'datetime',
    ];
     public function license(): HasOne {
        return $this->hasOne(License::class, 'purchase_code', 'purchase_code');
    }
}
