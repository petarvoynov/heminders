<?php

declare(strict_types=1);

namespace App\Enums;

use App\Checks\PostureCheck;
use App\Checks\StretchCheck;
use App\Checks\WaterCheck;

enum CheckType: string
{
    case Water = WaterCheck::class;
    case Stretch = StretchCheck::class;
    case Posture = PostureCheck::class;

    /**
     * Get the base for the given label.
     */
    public static function fromLabel(string $label): ?self
    {
        $label = mb_strtolower($label);

        return match ($label) {
            'water' => self::Water,
            'stretch' => self::Stretch,
            'posture' => self::Posture,
            default => null,
        };
    }

    /**
     * Get the labels for all the bases.
     *
     * @return array<int, string>
     */
    public static function labels(): array
    {
        return [
            self::Water->label(),
            self::Stretch->label(),
            self::Posture->label(),
        ];
    }

    /**
     * Get the label for the given base.
     */
    public function label(): string
    {
        return match ($this) {
            self::Water => 'water',
            self::Stretch => 'stretch',
            self::Posture => 'posture',
        };
    }
}
