<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 2/2/19
 * Time: 6:55 AM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;


use Dominikb\LaravelApiDocumentationGenerator\RouteParameter;
use Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModel;
use ReflectionParameter;

class RouteParameterTest extends TestCase
{
    /** @test */
    public function it_takes_a_reflection_parameter_and_returns_an_instance()
    {
        $reflectionParameter = new ReflectionParameter([$this, 'example'],'model');
        $parameter = RouteParameter::from($reflectionParameter);

        $this->assertInstanceOf(RouteParameter::class, $parameter);
        $this->assertEquals(TestModel::class, $parameter->getType());
        $this->assertEquals('model', $parameter->getName());
    }

    private function example(TestModel $model)
    {

    }
}