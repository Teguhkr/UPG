<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    protected $fillable = [
        'type',
        'title',
        'slug',
        'description',
        'file_path',
        'poster_path',
        'embed_url',
        'album',
        'sort_order',
        'is_published',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }
        if (str_starts_with($this->file_path, 'http') || str_starts_with($this->file_path, '/')) {
            return $this->file_path;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (! $this->poster_path) {
            return null;
        }
        if (str_starts_with($this->poster_path, 'http') || str_starts_with($this->poster_path, '/')) {
            return $this->poster_path;
        }

        return asset('storage/' . ltrim($this->poster_path, '/'));
    }
}

