<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends ApiRequest
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
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|regex:/^\d{0,6}(\.\d{1,2})?$/',
            'image' => 'sometimes|file|mimes:jpg,png,jpeg,gif,svg,pdf|max:5000',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
        ];
    }


    public function messages()
    {
        return [
            'name.string' => 'name field should be string',
            'price.numeric' => 'price field should be numeric',
            'price.regex' => 'price field should be float of 2 decimal place',
            'description.string' => 'description field should be string',
            'image.file' => 'image field should be a file',
            'image.mimes' => 'wrong image format:(jpg,png,jpeg,gif,svg,pdf)',
            'image.max' => 'image shoud not be more than 5MB',
            'category_ids.array' => 'category ids should be an array',
            'category_ids.exists' => 'category ids should be  valid category id',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $rules = $this->rules();
            $fields = array_filter(array_keys($rules), function ($field) use ($rules) {
                return strpos($field, '*') === false;
            });
            $hasAtLeastOne = false;
            foreach ($fields as $field) {
                if ($this->filled($field)) {
                    $hasAtLeastOne = true;
                    break;
                }
            }
            if (!$hasAtLeastOne) {
                $validator->errors()->add('at_least_one', 'At least one of ' . implode(', ', $fields) . ' must be present.');
            }
        });
    }
}
