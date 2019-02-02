<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 1/27/19
 * Time: 5:56 PM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests\App;

use Illuminate\Http\Request;
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

    public function update(ChangeTestModelNameRequest $req, TestModel $model)
    {
        $model->update($req->only('name'));

        return Response::json($model);
    }

    public function actionWithoutTypeHint($uuid)
    {

    }

    public function actionWithMultipleTypeHints(TestModel $model, string $uuid)
    {

    }

    public function actionWithRequestTypeHint(Request $req, TestModel $model)
    {

    }

    public function orderedTypes(Request $request, TestModel $model)
    {

    }
}