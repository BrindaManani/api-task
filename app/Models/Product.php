<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'id',
        'did',
        'product',
        'vid',
    ];
    public function update_downloads(): HasMany
    {
        return $this->hasMany(UpdateDownload::class, 'product', 'item_id');
    }

    public function buyer_profile(): BelongsTo
    {
        return $this->belongsTo(BuyerProfile::class);
    }
}
