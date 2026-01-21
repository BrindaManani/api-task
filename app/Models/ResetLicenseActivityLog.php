<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ResetLicenseActivityLog extends Model
{
     public function license(): HasOne {
        return $this->hasOne(License::class, 'purchase_code', 'purchase_code');
    }
}
