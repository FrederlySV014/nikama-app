<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'user_id',
    'favoritable_type',
    'favoritable_id',
])]
class Favorite extends Model
{
    use HasFactory, HasUuids;

    /**
     * Get the user who favorited the entity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the favorited entity.
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
