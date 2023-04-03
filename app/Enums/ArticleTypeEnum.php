<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;

enum ArticleTypeEnum: string
{
    use InvokableCases, Names;

    case general = 'general';
    case courses = 'courses';
    case tips = 'tips';

    public static function random(): self
    {
        return match (random_int(1, 3)) {
            1 => self::general,
            2 => self::courses,
            3 => self::tips,
        };
    }

    public function getId(): int
    {
        return match ($this) {
            self::general => 1,
            self::courses => 2,
            self::tips    => 3,
        };
    }

    public static function all(): array
    {
        return [
            self::general,
            self::courses,
            self::tips,
        ];
    }
}
