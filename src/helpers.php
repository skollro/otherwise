<?php

use Skollro\Otherwise\Match;

if (! function_exists('match')) {
    function match($value)
    {
        return new Match($value);
    }
}
