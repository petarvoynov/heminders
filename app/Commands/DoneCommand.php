<?php

declare(strict_types=1);

namespace App\Commands;

use App\Actions\MarkCheckAsDone;
use App\Enums\CheckType;
use LaravelZero\Framework\Commands\Command;

final class DoneCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'done {types?* : The types of checks to mark as done.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Marks all checks as done.';

    /**
     * Execute the console command.
     */
    public function handle(MarkCheckAsDone $action): void
    {
        $types = $this->argument('types');

        collect($types)
            ->filter(fn (string $type) => CheckType::fromLabel($type) === null)
            ->each(fn (string $type) => abort(
                1,
                sprintf('Invalid check type [%s]. Please use one of: %s.', $type, implode(', ', CheckType::labels()))
            ));

        $cases = count($types) > 0
            ? collect($types)->map(fn (string $type) => CheckType::fromLabel($type))->filter()->all()
            : CheckType::cases();

        collect($cases)
            ->each(fn (CheckType $type) => $action->handle($type));

        $this->info(sprintf('Marked [%s] checks as done.', collect($cases)->map(function (CheckType $type) {
            return $type->label();
        })->implode(', ')));
    }
}
