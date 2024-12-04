<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CheckType;
use Database\Factories\CheckFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Check extends Model
{
    /** @use HasFactory<CheckFactory> */
    use HasFactory;

    /**
     * Return the casts for the model.
     *
     * @return array{
     *     type: 'App\Enums\CheckType',
     * }
     */
    public function casts(): array
    {
        return [
            'type' => CheckType::class,
            'done_at' => 'datetime',
        ];
    }
}
