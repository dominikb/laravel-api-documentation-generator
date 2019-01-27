<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:14 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Exceptions\EmptyRoutesException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RouteParser {

    /** @var RouteCollection */
    private $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    /**
     * @param string $commandOutput
     * @return RouteCollection
     * @throws \Throwable
     */
    public function parse(string $commandOutput): RouteCollection
    {
        $this->guardAgainstInvalidInput($commandOutput);

        $this->extractRoutesFromInput($commandOutput);

        return $this->routes;
    }

    /**
     * @param string $commandOutput
     * @throws \Throwable
     */
    private function guardAgainstInvalidInput(string $commandOutput): void
    {
        throw_if(empty($commandOutput), new EmptyRoutesException);
    }

    private function extractRoutesFromInput(string $commandOutput): void
    {
        $lines = explode(PHP_EOL, $commandOutput);

        $routes = $this->stripFormattingAndWhitespace($lines)
                       ->map(function (string $routeString) {
                           return $this->routeFromString($routeString);
                       });

        $this->routes = $this->routes->merge($routes);
    }

    private function stripFormattingAndWhitespace(array $lines): Collection
    {
        return collect($lines)
            ->filter(function (string $line) {
                return Str::startsWith($line, '|');
            })
            ->slice(1)
            ->filter()
            ->map(function (string $line) {
                return str_replace(' ', '', $line);
            });
    }

    private function routeFromString(string $routeString): Route
    {
        $sections = collect(explode('|', $routeString))
            ->filter()
            ->values();

        $length = $sections->count();

        $controllerAndAction = explode('@', $sections->get($length - 2));
        $controller = array_first($controllerAndAction);
        $action = count($controllerAndAction) === 2 ? array_last($controllerAndAction) : '__invoke';

        $middleware = explode(',', $sections->get($length - 1));
        $endpoint = $sections->get($length - 4);
        $methods = $sections->slice(0, $length - 4)->toArray();

        return new Route($methods, $endpoint, $middleware, $controller, $action);
    }
}