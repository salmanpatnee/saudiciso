<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HROrganization extends Model
{
    use HasFactory;

    protected $table = 'hr_organization_table';

    protected $guarded = [];

    public $timestamps = false;

    public function industry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id', 'industry_id');
    }
}
