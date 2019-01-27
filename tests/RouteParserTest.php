<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:07 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;

use Dominikb\LaravelApiDocumentationGenerator\Exceptions\EmptyRoutesException;
use Dominikb\LaravelApiDocumentationGenerator\Route;
use Dominikb\LaravelApiDocumentationGenerator\RouteCollection;
use Dominikb\LaravelApiDocumentationGenerator\RouteParser;

class RouteParserTest
    extends TestCase {

    /** @var RouteParser */
    private $routeParser;

    /** @var string */
    private $commandOutput;

    /** @before */
    public function setUpRouteParseInstance()
    {
        $this->routeParser = new RouteParser;
    }

    /** @before */
    public function setUpCommandOutput()
    {
        $this->commandOutput = file_get_contents(__DIR__ . '/assets/routes.txt');
    }

    /** @test */
    public function given_an_empty_string_for_parsing_it_throws_an_exception()
    {
        $this->expectException(EmptyRoutesException::class);

        $this->routeParser->parse('');
    }

    /** @test */
    public function it_returns_a_collection_of_routes_when_parsing_was_successful()
    {
        $parsed = $this->routeParser->parse($this->commandOutput);

        $this->assertInstanceOf(RouteCollection::class, $parsed);
    }

    /** @test */
    public function the_number_of_input_routes_equals_the_size_of_the_returned_routes_collection()
    {
        $collection = $this->routeParser->parse($this->commandOutput);

        $this->assertCount(2, $collection);
    }

    /** @test */
    public function the_returned_routes_collection_only_contains_routes_objects()
    {
        $collection = $this->routeParser->parse($this->commandOutput);

        foreach($collection as $route) {
            $this->assertInstanceOf(Route::class, $route);
        }
    }
}