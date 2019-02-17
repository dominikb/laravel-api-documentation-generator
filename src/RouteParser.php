<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:14 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Contracts\RouteFormatter;
use Dominikb\LaravelApiDocumentationGenerator\Exceptions\EmptyRoutesException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class RouteParser {
    /** @var RouteFormatter */
    protected $formatter;

    /** @var RouteCollection */
    private $routes;

    public function __construct(RouteFormatter $formatter)
    {
        $this->routes = new RouteCollection;
        $this->formatter = $formatter;
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
            ->slice(1, -1)
            ->values();

        $length = $sections->count();

        [$controller, $action] = Str::parseCallback($sections->get($length - 2));

        $middleware = explode(',', $sections->get($length - 1));
        $endpoint = $sections->get($length - 4);

        $methods = $this->extractHttpMethods($routeString);

        return new Route($methods, $endpoint, $middleware, $controller, $action ?? '__invoke');
    }

    public function format(): string
    {
        return $this->routes
            ->map(function (Route $route) {
                return $this->formatter->format($route);
            })
            ->implode(PHP_EOL);
    }

    private function extractHttpMethods(string $routeString): array
    {
        $httpMethods = "/(GET|HEAD|OPTIONS|PUT|PATCH|POST|DELETE)(?=\|{1})/";
        $matches = Regex::matchAll($httpMethods, $routeString)
            ->results();
        $methods = collect($matches)->map->result()->all();

        return $methods;
    }
}