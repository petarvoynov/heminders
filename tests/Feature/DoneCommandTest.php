<?php

declare(strict_types=1);

use App\Commands\CheckMeCommand;
use App\Commands\DoneCommand;
use App\Enums\CheckType;
use App\Models\Check;
use App\Models\TrackedChecker;
use LaravelZero\Framework\Exceptions\ConsoleException;

it('should mark all checks as done', function () {
    TrackedChecker::factory()->create(['type' => CheckType::Water]);
    TrackedChecker::factory()->create(['type' => CheckType::Posture]);
    TrackedChecker::factory()->create(['type' => CheckType::Stretch]);

    $this->artisan(DoneCommand::class)
        ->expectsOutput('Marked [water, stretch, posture] checks as done.')
        ->assertExitCode(0);

    $this->artisan(CheckMeCommand::class)
        ->expectsOutput('You are fine. Keep up the good work!')
        ->assertExitCode(0);
});

it('bails out if the given check type is invalid', function () {
    TrackedChecker::factory()->create();
    $this->artisan(DoneCommand::class, ['types' => ['invalid']])->run();
})->throws(
    ConsoleException::class,
    'Invalid check type [invalid].',
);

it('marks the posture as done', function () {
    $this->travelTo($now = now());

    $this->artisan(DoneCommand::class, ['types' => ['water']])
        ->expectsOutput('Marked [water] checks as done.')
        ->assertExitCode(0)
        ->run();

    $check = Check::first();

    expect(Check::count())->toBe(1)
        ->and($check->type)->toBe(CheckType::Water)
        ->and($check->done_at->format('Y-m-d H:i:s'))->toBe($now->format('Y-m-d H:i:s'));
});

test('output with when the user posture check was done 1 hour ago', function () {
    TrackedChecker::factory()->create(['type' => CheckType::Stretch]);
    $this->artisan(DoneCommand::class, ['types' => ['stretch']])->run();

    $this->travel(1)->hours();

    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('Is posture correct? Straighten your back, shoulders relaxed, feet flat.')
        ->assertExitCode(0);
});

test('output with when the user stretch 8 hour ago', function () {
    TrackedChecker::factory()->create(['type' => CheckType::Posture]);
    $this->artisan(DoneCommand::class, ['types' => ['posture']])->run();

    $this->travel(8)->hours();

    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('Is posture correct? Straighten your back, shoulders relaxed, feet flat.')
        ->assertExitCode(0);
});
