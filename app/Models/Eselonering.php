<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eselonering extends Model
{
    protected $table = 'eselonering';

    public $timestamps = false;

    protected $guarded = [];

    public function satkers(): HasMany
    {
        return $this->hasMany(ListSatker::class, 'eselonering');
    }
}
