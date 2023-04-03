<?php

namespace App\Enums;

enum DiskEnum
{
    case local;
    case public;
    case private;
    case s3;
}
