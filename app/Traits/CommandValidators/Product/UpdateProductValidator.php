<?php

namespace App\Traits\CommandValidators\Product;

use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;
use Illuminate\Support\Arr;

trait UpdateProductValidator
{
    use BaseValidator, TabulateRecord;

    protected function validateArgumentsAndOptions ()
    {
        return $this->validate($this->getArgumentRules(), $this->getOptionRule());
    }


    protected function getArgumentRules () : array
    {
        return [
            'id' => ['required','exists:products,id']
        ];
    }

    protected function getOptionRule () : array
    {
        return [
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:categories,id']
        ];
    }

    protected function validateSelect(): array
    {
        $choices = ['Name', 'Description', 'Price', 'Image'];
        $selectedOptions = $this->choice(
            'Select one or more options you would like to update (comma-separated):',
            $choices,
            null,
            3,
            true
        );
        $questions = [
            'name' => 'Enter product name',
            'description' => 'Enter description name',
            'price' => 'Enter product price',
            'image' => 'Enter image full path/URL',
        ];
        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|regex:/^\d{0,6}(\.\d{1,2})?$/',
            'image' => 'required|string',
        ];
        $validatedData = [];
        foreach ($selectedOptions as $option) {
            $field = strtolower($option);
            $question = Arr::get($questions, $field);
            $rule = Arr::get($rules, $field);

            if ($question && $rule) {
                $value = $this->askAndValidate($question, $field, $rule);
                $validatedData[$field] = $value;
            }
        }
        return $validatedData;
    }

}