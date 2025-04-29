<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    /**
     * Get the user's first name.
     */
    protected function embeddings(): Attribute
    {
        return Attribute::make(
            get: fn(string|null $value) => $value ? str_replace(['[', ']'], '', explode(",", $value)) : [],
            set: fn (array $value) => '[' . implode(',', $value) . ']'
        );
    }
}
