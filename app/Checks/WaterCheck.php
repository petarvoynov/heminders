<?php

declare(strict_types=1);

namespace App\Checks;

use App\Contracts\Checkable;
use App\Enums\CheckType;
use App\Models\Check;
use App\ValueObjects\Advice;
use Illuminate\Support\Number;

final class WaterCheck implements Checkable
{
    /**
     * Check if I have drunk enough water.
     */
    public function check(): Advice
    {
        $lastCheck = Check::query()->where('type', CheckType::Water)->latest()->first();

        $glasses = $this->basedOn($lastCheck);

        if ($glasses <= 0) {
            return new Advice('');
        }

        return new Advice(
            sprintf(
                'You have not drunk enough water. You are %s %s behind.',
                Number::spell($glasses),
                str('glass')->plural($glasses),
            ),
        );
    }

    /**
     * Based on the last check, how many glasses should I drink?
     */
    private function basedOn(?Check $lastCheck): int
    {
        if (! $lastCheck) {
            return 1;
        }

        // @phpstan-ignore-next-line
        return (int) min($lastCheck->done_at->diffInHours(now()), 4);
    }
}
