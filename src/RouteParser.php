<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:14 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Contracts\Formatter;
use Dominikb\LaravelApiDocumentationGenerator\Exceptions\EmptyRoutesException;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class RouteParser {
    /** @var Formatter */
    protected $formatter;
    /** @var Router */
    protected $router;

    /** @var RouteCollection */
    private $routes;

    public function __construct(Router $router, Formatter $formatter)
    {
        $this->routes = new RouteCollection;
        $this->formatter = $formatter;
        $this->router = $router;
    }

    public function format(): string
    {
        $routes = $this->parseRoutes();

        return $this->formatter->format($routes);
    }

    private function parseRoutes(): RouteCollection
    {
        $routes = $this->router->getRoutes()->getRoutes();

        $mappedRoutes = array_map(function(\Illuminate\Routing\Route $route){
            return new Route(
                $route->methods(),
                $route->uri(),
                $route->middleware(),
                get_class($route->getController()),
                $route->getActionMethod()
            );
        }, $routes);

        return new RouteCollection($mappedRoutes);
    }
}