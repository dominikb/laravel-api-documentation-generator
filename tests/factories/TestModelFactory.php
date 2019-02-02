<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 2/2/19
 * Time: 7:55 AM
 */

use Dominikb\LaravelApiDocumentationGenerator\Tests\App\TestModel;
use Faker\Generator as Faker;

$factory->define(TestModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
