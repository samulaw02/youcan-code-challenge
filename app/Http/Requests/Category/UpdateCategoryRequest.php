<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends ApiRequest
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
            'name' => 'sometimes|string|unique:categories,name',
            'parent_category_id' => 'sometimes|exists:categories,id'
        ];
    }


    public function messages()
    {
        return [
            'name.string' => 'name field should be string',
            'name.unique' => 'name field should be unique',
            'parent_category_id.exists' => 'parent category should be a valid category id'
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
