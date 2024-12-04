<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CheckType;
use App\Models\Check;

final class MarkCheckAsDone
{
    /**
     * Run the action.
     */
    public function handle(CheckType $checkType): void
    {
        Check::create([
            'type' => $checkType,
            'done_at' => now(),
        ]);
    }
}
