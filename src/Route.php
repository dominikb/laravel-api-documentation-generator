<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:52 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

class Route {

    /** @var string[]|string */
    private $methods;

    /** @var string */
    private $endpoint;

    /** @var string[] */
    private $middleware;

    /** @var string */
    private $controller;

    /** @var string */
    private $action;

    public function __construct(array $methods, string $endpoint, array $middleware, string $controller,
                                string $action)
    {
        $this->methods = $methods;
        $this->endpoint = $endpoint;
        $this->middleware = $middleware;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getParameters(): array
    {
        $pattern = "({[\w_]*})";
        $parameters = [];

        preg_match_all($pattern, $this->endpoint, $parameters);

        $trimmed = array_map(function (string $parameter) {
            return substr($parameter, 1, strlen($parameter) - 2);
        }, $parameters[0]);

        return $trimmed;
    }

    /**
     * @return mixed
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

}