<?php

namespace Dominikb\LaravelApiDocumentationGenerator\Contracts;

use Dominikb\LaravelApiDocumentationGenerator\Route;

interface RouteFormatter
{
    function format(Route $route): string;
}