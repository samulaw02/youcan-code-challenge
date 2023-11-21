<?php

namespace App\Traits\CommandValidators\Product;

use App\Traits\CommandValidators\BaseValidator;

trait CreateProductValidator
{
    use BaseValidator;

    protected function promptAndValidate(): array
    {
        $questions = [
            'name' => 'Enter product name',
            'description' => 'Enter description name',
            'price' => 'Enter product price',
            'image' => 'Enter image full path/URL',
        ];

        $productData = [];

        foreach ($questions as $field => $question) {
            $productData[$field] = $this->askAndValidate($question, $field, $this->getValidationRules($field));
        }

        return $productData;
    }


    protected function getValidationRules($fieldName): string
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|regex:/^\d{0,6}(\.\d{1,2})?$/',
            'image' => 'required|string',
        ];

        return $rules[$fieldName] ?? '';
    }
}
