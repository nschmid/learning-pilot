<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = ['user_id', 'step_id'];

    protected $fillable = [
        'user_id',
        'step_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    // Override getKey for composite primary keys
    public function getKey()
    {
        return [$this->user_id, $this->step_id];
    }

    // Required for composite primary key
    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('user_id', $this->user_id)
            ->where('step_id', $this->step_id);
    }
}
