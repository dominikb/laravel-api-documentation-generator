<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 5:15 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;

use Dominikb\LaravelApiDocumentationGenerator\Route;

class RouteTest
    extends TestCase {

    private function makeRoute($params = [])
    {
        $routeParams = array_merge([
            'methods' => ['GET'],
            'endpoint' => 'api/test-model',
            'middleware' => ['api'],
            'controller' => 'App\Http\Controller\TestModelController',
            'action' => 'index',
        ], $params);

        return new Route(...array_values($routeParams));
    }

    /** @test */
    public function it_can_extract_parameter_names_from_the_endpoint()
    {
        $route = $this->makeRoute(['endpoint' => 'api/{id}/{id2}']);

        $this->assertEquals(['id', 'id2'], $route->getParameters());
    }
}