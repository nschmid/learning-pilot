<?php

namespace App\Events;

use App\Models\Enrollment;
use App\Models\LearningStep;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepCompleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Enrollment $enrollment,
        public LearningStep $step
    ) {}
}
