<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CheckType;
use App\Models\TrackedChecker;
use Illuminate\Support\Collection;

final class TrackedCheckerStore
{
    /**
     * Run the action.
     *
     * @param  Collection<int, CheckType>  $checkers
     */
    public function handle(Collection $checkers): void
    {
        $checkers->each(fn (CheckType $checker) => TrackedChecker::create([
            'type' => $checker,
        ]));
    }
}
