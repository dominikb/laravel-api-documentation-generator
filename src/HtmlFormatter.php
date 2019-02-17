<?php


namespace Dominikb\LaravelApiDocumentationGenerator;


use Dominikb\LaravelApiDocumentationGenerator\Contracts\Formatter;
use Illuminate\Support\Arr;

class HtmlFormatter implements Formatter
{

    function format(RouteCollection $collection): string
    {
        $content = $collection
            ->map(function (Route $route, $index) {
                return $this->formatRoute($route, $index + 1) . "<br>";
            })
            ->implode("");

        return $this->wrap($content);
    }

    private function formatRoute(Route $route, int $index): string
    {
        $methods = join('|', Arr::wrap($route->getMethods()));
        return <<<HTML
<h3>$index. [$methods] {$route->getEndpoint()}</h3>
<div>
<p>{$route->getController()}@{$route->getAction()}</p>
{$this->middleware($route)}
{$this->parameters($route)}
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

    private function middleware(Route $route)
    {
        $list = array_map(function(string $middleware) {
            return "<li>{$middleware}</li>";
        }, $route->getMiddleware());

        $list = implode('', $list);

        return <<<HTML
<p>
<h4>Middleware</h4>
<ul>
$list
</ul>
</p>
HTML;

    }

    private function parameters(Route $route)
    {
        $list = [];
        foreach($route->getParameterTypeMap() as $name => $type) {
            $list[] = "<li>$$name > $type</li>";
        }

        $list = implode('', $list);

        return <<<HTML
<p>
<h4>Parameters</h4>
<ul>
$list
</ul>
</p>
HTML;
    }
}