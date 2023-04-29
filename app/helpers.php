<?php
if (! function_exists('origin')) {
    function origin()
    {
        return $_SERVER['HTTP_ORIGIN'] ?? null;
    }
}

if (! function_exists('random')) {
    function random(...$args):string
    {
        $args = collect($args)->flatten()->toArray();

        return $args[array_rand($args)];

    }
}
