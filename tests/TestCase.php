<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 4:02 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Schema\Blueprint;

class TestCase
    extends \Orchestra\Testbench\TestCase {

    protected function setUp()
    {
        parent::setUp();

        /** @var DatabaseManager $db */
        $db = $this->app['db'];

        $db->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $this->withFactories(__DIR__.'/factories');
    }

}