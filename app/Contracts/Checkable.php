<?php

declare(strict_types=1);

namespace App\Contracts;

use App\ValueObjects\Advice;

interface Checkable
{
    /**
     * Runs the check.
     */
    public function check(): Advice;
}
