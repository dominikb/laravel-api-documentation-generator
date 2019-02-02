<?php
/**
 * Created by IntelliJ IDEA.
 * User: dominik
 * Date: 2/2/19
 * Time: 8:00 AM
 */

namespace Dominikb\LaravelApiDocumentationGenerator\Tests\App;


use Illuminate\Foundation\Http\FormRequest;

class ChangeTestModelNameRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'string|max:100'
        ];
    }   
}