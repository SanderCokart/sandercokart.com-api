<?php

namespace App\Enums;

enum Disk
{
    case local;
    case public;
    case private;
    case s3;
}
