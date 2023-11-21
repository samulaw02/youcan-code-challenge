<?php
namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;


abstract class ApiRequest extends LaravelFormRequest {

    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    abstract public function rules();


    /**
     * Determine if user is authorized to make this request
     *
     * @return bool;
     */
    abstract public function authorize();


    /**
     * Handle a failed validation request
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator) {
        $errors = (new ValidationException($validator))->errors();
        $msg = implode(" ", array_values($errors)[0]);
        // $msg = array_values($errors);
        throw new HttpResponseException (
            response()->json(["status" => false, "message" => "an error has occured", "error" => $msg], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
