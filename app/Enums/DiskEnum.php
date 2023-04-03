<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

/**
 * @method static string local()
 * @method static string public()
 * @method static string private()
 * @method static string s3()
 */
enum DiskEnum
{
    use InvokableCases;

    case local;
    case public;
    case private;
    case s3;
}
