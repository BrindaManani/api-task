<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseActivityLog extends Model
{
    protected $fillable = [
        'id',
        'license_id',
        'action',
        'ip_address'
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
