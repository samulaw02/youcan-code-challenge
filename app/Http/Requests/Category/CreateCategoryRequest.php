<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\ApiRequest;

class CreateCategoryRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:categories,name',
            'parent_category_id' => 'sometimes|exists:categories,id'
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'name field is required',
            'name.string' => 'name field should be string',
            'name.unique' => 'name field should be unique',
            'parent_category_id.exists' => 'parent category should be a valid category id'
        ];
    }
}
