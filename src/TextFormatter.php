<?php

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Contracts\RouteFormatter;

class TextFormatter implements RouteFormatter
{
    function format(Route $route): string
    {
        return $route->__toString();
    }
}