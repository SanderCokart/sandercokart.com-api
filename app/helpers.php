<?php
if (! function_exists('origin')) {
    function origin()
    {
        return $_SERVER['HTTP_ORIGIN'] ?? null;
    }
}
