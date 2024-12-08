<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\MarkCheckAsDone;
use App\Actions\TrackedCheckerStore;
use App\Contracts\Checkable;
use App\Enums\CheckType;
use App\Models\TrackedChecker;
use App\ValueObjects\Advice;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;

final class CheckMeCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'check:me';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Checks if I am fine.';

    /**
     * Execute the console command.
     */
    public function handle(MarkCheckAsDone $action, TrackedCheckerStore $trackedCheckerStoreAction): void
    {
        $this->handleEmptyTrackersCase($trackedCheckerStoreAction);

        $checkers = collect(CheckType::cases())
            ->map(fn (CheckType $type) => app($type->value));

        $advicesGiven = $checkers
            ->map(fn (Checkable $checker) => $checker->check())
            ->map(fn (Advice $advice) => $advice->message)
            ->reject(fn (string $message) => $message === '')
            ->each(fn (string $message) => $this->info($message))
            ->count();

        if ($advicesGiven === 0) {
            $this->info('You are fine. Keep up the good work!');

            return;
        }

        if (confirm('Mark all checks as done?')) {
            $checkers
                ->reject(fn (Checkable $checker) => $checker->check()->message === '')
                ->each(fn (Checkable $checker) => $action->handle(
                    CheckType::from($checker::class),
                ));
        }
    }

    private function handleEmptyTrackersCase(TrackedCheckerStore $action): void
    {

        $trackedCheckers = TrackedChecker::query()->get();

        if ($trackedCheckers->isEmpty()) {

            $selectedTrackedCheckers = collect(multiselect(
                label: 'No checkers are currently set. Please select the checkers you want to run:',
                options: CheckType::labels(),
                hint: 'You can update the selected checkers at any time using the [XXX: TODO:] command.',
                scroll: 10,
                required: true
            ))
                ->map(fn (int|string $checker): ?CheckType => CheckType::fromLabel((string) $checker))
                ->filter();

            $action->handle($selectedTrackedCheckers);

            $joinedTrackedCheckers = $selectedTrackedCheckers->map(fn (CheckType $selectedChecker): string => $selectedChecker->label())->join(' | ');
            $this->info("You've selected: [{$joinedTrackedCheckers}]. These checkers will now be tracked.");
        }
    }
}
