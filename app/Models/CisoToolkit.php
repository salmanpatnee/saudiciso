<?php

namespace App\Models;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CisoToolkit extends Model
{
    use HasFactory;

    protected $table = 'ciso_toolkit';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'category' => Category::class,
        ];
    }
}
