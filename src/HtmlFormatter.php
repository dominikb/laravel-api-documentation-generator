<?php


namespace Dominikb\LaravelApiDocumentationGenerator;


use Dominikb\LaravelApiDocumentationGenerator\Contracts\Formatter;
use Illuminate\Support\Arr;

class HtmlFormatter implements Formatter
{

    function format(RouteCollection $collection): string
    {
        $content = $collection
            ->map(function (Route $route) {
                return $this->formatRoute($route);
            })
            ->implode("");

        return $this->wrap($content);
    }

    private function formatRoute(Route $route): string
    {
        $methods = join(', ', Arr::wrap($route->getMethods()));
        return <<<HTML
<h3>{$route->getEndpoint()}</h3>
<div>
{$methods}
</div>
HTML;
    }

    private function wrap(string $content): string
    {
        return <<<HTML
<html>
<body>
$content
</body>
</html>
HTML;

    }
}