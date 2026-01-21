<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateDownload extends Model
{
    protected $fillable = [
        'id',
        'vid',
        'pid',
        'version',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
