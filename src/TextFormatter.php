<?php

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Contracts\Formatter;

class TextFormatter implements Formatter
{
    function format(RouteCollection $route): string
    {
        return $route->map->__toString()->implode(PHP_EOL);
    }
}