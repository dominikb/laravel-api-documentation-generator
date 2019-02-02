<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:52 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Exceptions\ParameterNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionParameter;

class Route
{

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

    public function __construct(
        array $methods,
        string $endpoint,
        array $middleware,
        string $controller,
        string $action
    ) {
        $this->methods = $methods;
        $this->endpoint = $endpoint;
        $this->middleware = $middleware;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getParameterTypeMap(): array
    {
        $actionParameters = $this->getActionParameters();
        $routeParameterNames = $this->getParameterNames();

        if (array_first($actionParameters)->getClass()->newInstanceWithoutConstructor() instanceof Request) {
            $routeParameterNames = Arr::prepend($routeParameterNames, 'request');
        }

        return collect($routeParameterNames)
            ->zip($actionParameters)
            ->mapWithKeys(function($tuple) {
                return [$tuple[0] => RouteParameter::from($tuple[1])->getType()];
            })
            ->toArray();
    }

    public function getParameterNames(): array
    {
        $pattern = "({[\w_]*})";
        $parameters = [];

        preg_match_all($pattern, $this->endpoint, $parameters);

        $trimmed = array_map(function (string $parameter) {
            return substr($parameter, 1, strlen($parameter) - 2);
        }, $parameters[0]);

        return $trimmed;
    }

    public function getParameter(string $parameterName): RouteParameter
    {
        throw_if(! in_array($parameterName, $this->getParameterNames()), new ParameterNotFoundException);

        $parameter = $this->resolveParameterByName($parameterName);

        if ( ! $parameter) {
            $parameter = $this->resolveParameterByOrder($parameterName);
        }

        throw_if(! $parameter instanceof ReflectionParameter, new ParameterNotFoundException);

        return RouteParameter::from($parameter);
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

    public function __toString()
    {
        $methods = join('|', Arr::wrap($this->methods));

        $output = "[$methods] $this->endpoint" . PHP_EOL;

        $output .= "$this->controller@$this->action" . PHP_EOL;

        $middleware = collect($this->middleware)
            ->map(function ($middleware) {
                return "- '$middleware'" . PHP_EOL;
            })
            ->implode(PHP_EOL);
        $output .= "Middleware:" . PHP_EOL . $middleware;

        $parameters = collect($this->getParameterTypeMap())
            ->map(function ($type, $parameter) {
                if (class_exists($type)) {
                    $reflector = new ReflectionClass($type);

                    /** @var Model $inst */
                    $inst = $reflector->newInstance();

                    $description = "Primary Key [{$inst->getKeyName()}] of $type";
                } else {
                    $description = $type;
                }

                return "- $parameter <> '$description'";
            })
            ->implode(PHP_EOL);
        $output .= "Parameters:" . PHP_EOL . $parameters;

        return $output;
    }

    /**
     * @param string $parameterName
     *
     * @return ReflectionParameter|null
     * @throws \ReflectionException
     */
    private function resolveParameterByName(string $parameterName): ?ReflectionParameter
    {
        $parameters = $this->getActionParameters();

        $parameter = collect($parameters)
            ->first(function (ReflectionParameter $parameter) use ($parameterName) {
                return $parameter->getName() === $parameterName;
            });

        return $parameter;
    }

    private function resolveParameterByOrder(string $parameterName)
    {
        $parameterNames = $this->getParameterNames();
        $reflectionParameters = $this->getActionParameters();

        $parameter = collect($parameterNames)
            ->zip($reflectionParameters)
            ->mapWithKeys(function ($tuple) {
                return [$tuple[0] => $tuple[1]];
            })
            ->get($parameterName);

        return $parameter;
    }

    /**
     * @return ReflectionParameter[]
     * @throws \ReflectionException
     */
    private function getActionParameters()
    {
        $reflector = new ReflectionClass($this->controller);

        $parameters = $reflector->getMethod($this->action)->getParameters();

        $parameters = collect($parameters)
            ->reject(function (ReflectionParameter $parameter) {
                // Keep built-in types
                if ( ! $class = $parameter->getClass()) {
                    return;
                }

                $parameter = $class->newInstanceWithoutConstructor();

                // Filter type-hints of BaseRequest class
                return $parameter instanceof Request && ! $parameter instanceof FormRequest;
            })
            ->toArray();

        return $parameters;
    }
}