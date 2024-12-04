<?php

declare(strict_types=1);

use App\Models\Check;

test('to array', function () {
    $check = Check::factory()->create()->fresh();

    expect($check->toArray())->toHaveKeys([
        'id',
        'type',
        'done_at',
        'created_at',
        'updated_at',
    ]);
});
