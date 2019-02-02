<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 6:59 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator;

use ReflectionParameter;

class RouteParameter {

    /** @var ReflectionParameter */
    protected $parameter;

    public static function from(ReflectionParameter $parameter): RouteParameter
    {
        return new self($parameter);
    }

    private function __construct(?ReflectionParameter $parameter) {
        $this->parameter = $parameter;
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function getType(): ?string
    {
        if (! $this->parameter->hasType()) {
            return null;
        }

        return $this->parameter->getType()->getName();
    }
}