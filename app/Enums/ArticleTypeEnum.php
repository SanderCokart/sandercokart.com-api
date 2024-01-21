<?php

namespace App\Enums;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;

/**
 * @method static string general()
 * @method static string courses()
 * @method static string tips()
 */
enum ArticleTypeEnum: string
{
    use InvokableCases, Names, From;

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

    public static function all(): array
    {
        return array_map(fn($name) => self::from($name), self::names());
    }

    public static function getAssocArray(?callable $callback): array
    {
        return collect(self::all())->mapWithKeys(fn(self $articleType) => [$articleType->getId() => self::formatString($callback, $articleType())])->toArray();
    }

    private static function formatString(?callable $callback, string $string): string
    {
        return $callback ? $callback($string) : $string;
    }

    public static function fromId(int $id): self
    {
        return match ($id) {
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
}
