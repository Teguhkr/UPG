<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListSatker extends Model
{
    protected $table = 'list_satker';

    public $timestamps = false;

    protected $guarded = [];

    public function eselon(): BelongsTo
    {
        return $this->belongsTo(Eselonering::class, 'eselonering');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'satker_id');
    }
}