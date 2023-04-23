<?php
if (! function_exists('origin')) {
    function origin()
    {
        return $_SERVER['HTTP_ORIGIN'] ?? null;
    }
}

if (! function_exists('random')) {
    function random(...$args)
    {
        return $args[array_rand($args)];
    }
}
