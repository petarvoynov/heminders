<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\MarkCheckAsDone;
use App\Contracts\Checkable;
use App\Enums\CheckType;
use App\ValueObjects\Advice;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;

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
    public function handle(MarkCheckAsDone $action): void
    {
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
}
