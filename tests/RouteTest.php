<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 5:15 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;

use Dominikb\LaravelApiDocumentationGenerator\Exceptions\ParameterNotFoundException;
use Dominikb\LaravelApiDocumentationGenerator\Route;
use Dominikb\LaravelApiDocumentationGenerator\Tests\App\ChangeTestModelNameRequest;
use Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModel;
use Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModelController;

class RouteTest
    extends TestCase {

    private function makeRoute($params = [])
    {
        $routeParams = array_merge([
            'methods'    => ['GET'],
            'endpoint'   => 'api/test-models/{model}',
            'middleware' => ['api'],
            'controller' => TestModelController::class,
            'action'     => 'show',
        ], $params);

        return new Route(...array_values($routeParams));
    }

    /** @test */
    public function it_extracts_parameter_names_from_the_endpoint()
    {
        $route = $this->makeRoute(['endpoint' => 'api/{id}/{id2}']);

        $this->assertEquals(['id', 'id2'], $route->getParameterNames());
        $this->assertCount(2, $route->getParameterNames());
    }

    /** @test */
    public function given_a_parameter_it_can_resolve_its_type()
    {
        $route = $this->makeRoute();

        $this->assertEquals(TestModel::class, $route->getParameter('model')->getType());
    }

    /** @test */
    public function given_a_non_existing_parameter_an_exception_gets_thrown()
    {
        $this->expectException(ParameterNotFoundException::class);

        $this->makeRoute()->getParameter('invalid parameter');
    }

    /** @test */
    public function given_a_route_when_a_parameters_does_not_match_by_name_it_assigns_them_by_order()
    {
        $route = $this->makeRoute([
            'endpoint' => '/test-model/{id}',
            'action'   => 'orderedTypes',
        ]);

        $this->assertEquals([
            'id' => TestModel::class,
        ], $route->getParameterTypeMap());
    }

    /** @test */
    public function it_returns_null_for_parameters_without_a_type()
    {
        $route = $this->makeRoute([
            'endpoint' => '/action/{uuid}',
            'action' => 'actionWithoutTypeHint',
        ]);

        $this->assertNull($route->getParameter('uuid')->getType());
    }

    /** @test */
    public function it_also_returns_the_parameter_type_if_the_request_object_is_type_hinted()
    {
        $route = $this->makeRoute([
            'action' => 'actionWithRequestTypeHint',
        ]);

        $this->assertEquals(TestModel::class, $route->getParameter('model')->getType());
    }

    /** @test */
    public function it_retrieves_types_for_multiple_parameters()
    {
        $route = $this->makeRoute([
            'endpoint' => '/api/test-models/{model}/{uuid}',
            'action' => 'actionWithMultipleTypeHints',
        ]);

        $shouldReceive = [
            'model' => TestModel::class,
            'uuid'  => 'string',
        ];

        $this->assertEquals($shouldReceive, $route->getParameterTypeMap());
    }

    /** @test */
    public function it_implements_a_to_string_method()
    {
        $route = $this->makeRoute([
            'methods'    => ['GET', 'OPTION'],
            'endpoint'   => 'api/test-model/{model}',
            'middleware' => ['api'],
            'controller' => TestModelController::class,
            'action'     => 'show',
        ]);

        $expectedFormat = <<<DOC
[GET|OPTION] api/test-model/{model}
Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModelController@show
Middleware:
- 'api'
Parameters:
- \$model <> 'Primary Key [id] of Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModel'
DOC;
        $this->assertEquals($expectedFormat, $route->__toString());
    }

    /** @test */
    public function it_strips_the_injection_of_a_non_form_request_from_its_parameters()
    {
        $route = $this->makeRoute([
            'action' => 'actionWithRequestTypeHint',
        ]);

        $this->assertEquals(['model' => TestModel::class], $route->getParameterTypeMap());
    }

    /** @test */
    public function it_keeps_form_request_objects()
    {
        factory(TestModel::class)->create();

        $route = $this->makeRoute([
            'action'   => 'update',
            'endpoint' => 'api/test-model/{id}',
        ]);

        $typeMap = [
            'request' => ChangeTestModelNameRequest::class,
            'id'   => TestModel::class,
        ];
        $this->assertEquals($typeMap, $route->getParameterTypeMap());
    }
}