<?php

declare(strict_types=1);

namespace App\Checks;

use App\Contracts\Checkable;
use App\Enums\CheckType;
use App\Models\Check;
use App\ValueObjects\Advice;
use Illuminate\Support\Number;

final class StretchCheck implements Checkable
{
    /**
     * Check if I have stretched enough.
     */
    public function check(): Advice
    {
        $lastCheck = Check::query()->where('type', CheckType::Stretch)->latest()->first();

        $minutes = $this->basedOn($lastCheck);

        if ($minutes <= 0) {
            return new Advice('');
        }

        return new Advice(
            sprintf(
                'Time to stretch! You should stretch for %s %s. Neck stretches, wrist rolls, or back stretches.',
                Number::spell($minutes), str('minute')->plural($minutes),
            ),
        );
    }

    /**
     * Based on the last check, how many minutes should I stretch?
     */
    private function basedOn(?Check $lastCheck): int
    {
        if (! $lastCheck) {
            return 1;
        }

        // @phpstan-ignore-next-line
        return (int) min($lastCheck->done_at->diffInHours(now()), 8);
    }
}
