<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'version',
        'download_count',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getExtensionAttribute(): string
    {
        if ($this->external_url && ! $this->file_path) {
            return 'LINK';
        }

        return strtoupper(pathinfo($this->file_name ?: $this->file_path ?: 'file', PATHINFO_EXTENSION) ?: 'FILE');
    }
}

