<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\ApiRequest;


class CreateProductRequest extends ApiRequest
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
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|regex:/^\d{0,6}(\.\d{1,2})?$/',
            'image' => 'file|mimes:jpg,png,jpeg,gif,svg,pdf|max:5000',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'name field is required',
            'name.string' => 'name field should be string',
            'price.required' => 'price field is required',
            'price.regex' => 'price field should be float of 2 decimal place',
            'description.required' => 'description field is required',
            'description.string' => 'description field should be string',
            'image.file' => 'image field should be a file',
            'image.mimes' => 'wrong image format:(jpg,png,jpeg,gif,svg,pdf)',
            'image.max' => 'image shoud not be more than 5MB',
            'category_ids.required' => 'category ids field is required',
            'category_ids.array' => 'category ids should be an array',
            'category_ids.exists' => 'category ids should be valid category id',
        ];
    }
}
