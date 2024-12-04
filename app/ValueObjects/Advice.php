<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Advice
{
    /**
     * Create a new Advice instance.
     */
    public function __construct(
        public string $message,
    ) {}
}
