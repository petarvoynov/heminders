<?php

declare(strict_types=1);

use App\Commands\CheckMeCommand;
use App\Commands\DoneCommand;
use App\Enums\CheckType;
use App\Models\Check;
use App\Models\TrackedChecker;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

test('default output with no checks', function () {
    TrackedChecker::factory()->create(['type' => CheckType::Water]);

    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('You have not drunk enough water. You are one glass behind.')
        ->assertExitCode(0);
});

test('may mark everything as done right at the end', function () {
    TrackedChecker::factory()->create();
    $this->artisan(DoneCommand::class, ['types' => ['posture']])->run();

    expect(Check::count())->toBe(1);

    $this->artisan(CheckMeCommand::class, ['--no-interaction' => true])
        ->expectsOutput('You have not drunk enough water. You are one glass behind.')
        ->assertExitCode(0);

    expect(Check::count())->toBe(count(CheckType::cases()));
});

test('default output when all checks were done literally now', function () {
    TrackedChecker::factory()->create();
    $this->artisan(DoneCommand::class)->run();

    $this->artisan(CheckMeCommand::class)
        ->expectsOutput('You are fine. Keep up the good work!')
        ->assertExitCode(0);
});

// https://github.com/laravel/prompts/blob/main/tests/Feature/MultiSelectPromptTest.php
test('default will ask to select check trackers on first run', function () {
    Prompt::fake([
        // MultiSelect for check trackers
        // No checkers are currently set. Please select the checkers you want to run:
        Key::SPACE, // choose water
        Key::DOWN,
        Key::SPACE, // choose stretch
        Key::ENTER,

        // Mark all checks as done?
        Key::RIGHT,
        Key::ENTER, // no
    ]);

    expect(TrackedChecker::count())->toBe(0);

    $this->artisan(CheckMeCommand::class)
        ->expectsOutput("You've selected: [water | stretch]. These checkers will now be tracked.")
        ->assertExitCode(0);

    expect(TrackedChecker::count())->toBe(2);
    expect(TrackedChecker::query()->where('type', CheckType::Water->value)->exists())->toBeTrue();
    expect(TrackedChecker::query()->where('type', CheckType::Stretch->value)->exists())->toBeTrue();
});
