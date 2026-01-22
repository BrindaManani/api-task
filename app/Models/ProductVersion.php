<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVersion extends Model
{
    protected $fillable = [
        'id',
        'vid',
        'pid',
        'version',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
