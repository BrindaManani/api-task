<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'last_validate_request',
    ];

    public function product_versions(): HasMany
    {
        return $this->hasMany(ProductVersion::class, 'pid', 'item_id');
    }
    public function reset_license_activity_log(): HasOne {
        return $this->hasOne(ResetLicenseActivityLog::class, 'purchase_code', 'purchase_code');
    }
}
