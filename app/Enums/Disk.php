<?php

namespace App\Enums;

enum Disk
{
    case local;
    case public;
    case private;
    case publishedArticles;
    case privateArticles;
    case s3;
}
