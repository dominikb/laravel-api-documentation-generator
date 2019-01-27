<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 5:56 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests\App;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class TestModelController
    extends Controller {

    public function index()
    {
        $models = TestModel::all();

        return Response::json($models);
    }

    public function show(TestModel $model)
    {
        return Response::json($model);
    }
}