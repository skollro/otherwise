<?php

namespace Skollro\Otherwise;

function match($value, ...$params)
{
    return Match::value($value, ...$params);
}
