<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModuleDependency extends Pivot
{
    protected $table = 'module_dependencies';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'module_id',
        'required_module_id',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function requiredModule(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'required_module_id');
    }
}
