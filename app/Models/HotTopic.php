<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HotTopic extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function resources(): MorphMany
    {
        return $this->morphMany(Resource::class, 'resourceable');
    }
}
