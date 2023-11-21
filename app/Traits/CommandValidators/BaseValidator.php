<?php

namespace App\Traits\CommandValidators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;


Trait BaseValidator
{

    protected function askAndValidate($question, $field, $rules, $maxAttempts = 3)
    {
        $attempts = 0;
        do {
            $value = $this->ask($question);
            if ($message = $this->validateInput($rules, $field, $value)) {
                $attempts++;
                $attemptsLeft = $maxAttempts - $attempts;
                if ($attemptsLeft) {
                    $this->error("Validation error: $message. Attempts left: $attemptsLeft");
                }
            } else {
                return $value;
            }
        } while ($attempts < $maxAttempts);
        $this->error("Exceeded maximum attempts. Exiting.");
        exit(0);
    }


    protected function validateInput($rules, $fieldName, $value)
    {
        if ($fieldName == 'image')
        {
            $isImageValid = filter_var($value, FILTER_VALIDATE_URL) || File::exists($value);
            return !$isImageValid ? 'Invalid image URL/path' : null;
        }
        $validator = Validator::make([$fieldName => $value], [$fieldName => $rules]);
        return $validator->fails() ? $validator->errors()->first($fieldName) : null;
    }

    public function validate(array $argumentRules = null, $optionRules = null): array
    {
        $arguments = $argumentRules ? $this->validateArguments($this->arguments(), $argumentRules) : $this->arguments();
        $options = $optionRules ? $this->validateOptions($this->options(), $optionRules) : $this->options();

        return [$arguments, $options];
    }

    protected function validateOptions(array $options = [], array $rules = []): ?array
    {
        $validator = Validator::make($options, $rules);

        if ($validator->fails()) {
            $this->error('Whoops! The given options are invalid.');
            collect($validator->errors()->all())->each(fn($error) => $this->line($error));
            exit(0);
        }

        return $validator->validated();
    }

    protected function validateArguments(array $arguments = [], array $rules = []): ?array
    {
        // Remove the "command" key
        unset($arguments['command']);
        $validator = Validator::make($arguments, $rules);

        if ($validator->fails()) {
            $this->error('Whoops! The given attributes are invalid.');
            collect($validator->errors()->all())->each(fn($error) => $this->line($error));
            exit(0);
        }

        return $validator->validated();
    }
}
