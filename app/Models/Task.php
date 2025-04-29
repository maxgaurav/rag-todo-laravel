<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Task extends Model
{
    protected $guarded = [];

    /**
     * Get the user's first name.
     */
    protected function embeddings(): Attribute
    {
        return Attribute::make(
            get: fn(string|null $value) => (new Collection($value ? str_replace(['[', ']'], '', explode(",", $value)) : []))->map(fn($item) => floatval($item))->toArray(),
            set: fn(array $value) => '[' . implode(',', $value) . ']'
        );
    }
}
