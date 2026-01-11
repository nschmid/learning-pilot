<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Prerequisite extends Pivot
{
    protected $table = 'prerequisites';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'learning_path_id',
        'required_path_id',
    ];

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }

    public function requiredPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'required_path_id');
    }
}
