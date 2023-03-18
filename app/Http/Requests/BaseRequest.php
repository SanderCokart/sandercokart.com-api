<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class BaseRequest extends Request
{
    public function expectsJson(): true
    {
        return true;
    }

    public function wantsJson(): true
    {
        return true;
    }
}
