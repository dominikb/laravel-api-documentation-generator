<?php

namespace Dominikb\LaravelApiDocumentationGenerator\Contracts;

use Dominikb\LaravelApiDocumentationGenerator\RouteCollection;

interface Formatter
{
    function format(RouteCollection $route): string;
}