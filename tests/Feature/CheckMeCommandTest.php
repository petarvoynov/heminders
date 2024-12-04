<?php

declare(strict_types=1);

use App\Commands\CheckMeCommand;
use App\Commands\DoneCommand;
use App\Enums\CheckType;
use App\Models\Check;

test('default output with no checks', function () {
    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('You have not drunk enough water. You are one glass behind.')
        ->assertExitCode(0);
});

test('may mark everything as done right at the end', function () {
    $this->artisan(DoneCommand::class, ['types' => ['posture']])->run();

    expect(Check::count())->toBe(1);

    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('You have not drunk enough water. You are one glass behind.')
        ->assertExitCode(0);

    expect(Check::count())->toBe(count(CheckType::cases()));
});

test('default output when all checks were done literally now', function () {
    $this->artisan(DoneCommand::class)->run();

    $this->artisan(CheckMeCommand::class)
        ->expectsOutput('You are fine. Keep up the good work!')
        ->assertExitCode(0);
});
