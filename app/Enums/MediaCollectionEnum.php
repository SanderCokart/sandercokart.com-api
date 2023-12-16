<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

/**
 * @method static string ArticleBanners()
 * @method static string CourseBanners()
 */
enum MediaCollectionEnum
{
    use InvokableCases;

    case ArticleBanners;
    case CourseBanners;
}
