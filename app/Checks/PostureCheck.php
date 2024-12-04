<?php

declare(strict_types=1);

namespace App\Checks;

use App\Contracts\Checkable;
use App\Enums\CheckType;
use App\Models\Check;
use App\ValueObjects\Advice;

final class PostureCheck implements Checkable
{
    /**
     * Check if I have stretched enough.
     */
    public function check(): Advice
    {
        $lastCheck = Check::query()->where('type', CheckType::Posture)->latest()->first();

        if (! $this->shouldAdviceBeGiven($lastCheck)) {
            return new Advice('');
        }

        return new Advice(
            'Is posture correct? Straighten your back, shoulders relaxed, feet flat.',
        );
    }

    /**
     * Based on the given last check, determine if advice should be given.
     */
    private function shouldAdviceBeGiven(?Check $lastCheck): bool
    {
        // @phpstan-ignore-next-line
        return is_null($lastCheck) || $lastCheck->done_at->diffInMinutes(now()) >= 30;
    }
}
