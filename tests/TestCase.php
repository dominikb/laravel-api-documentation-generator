<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:02 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;

class TestCase
    extends \Orchestra\Testbench\TestCase {

    protected function setUp()
    {
        parent::setUp();

        $this->setUpDumpServer();
    }

    private function setUpDumpServer()
    {

    }
}