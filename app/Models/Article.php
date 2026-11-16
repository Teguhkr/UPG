<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_featured',
        'is_published',
        'published_at',
        'hits',
        'sort_order',
        'author_id',
        'meta_keywords',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }
        if (str_starts_with($this->featured_image, 'http') || str_starts_with($this->featured_image, '/')) {
            return $this->featured_image;
        }

        return asset('storage/' . ltrim($this->featured_image, '/'));
    }
}

