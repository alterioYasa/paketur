<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => $this->isMethod('POST') ?  'required|unique:companies,name' : ['required', Rule::unique('companies', 'name')->ignore($this->route('id'))],
            'email' => $this->isMethod('POST') ? 'required|unique:companies,email' : ['required', Rule::unique('companies', 'email')->ignore($this->route('id'))],
            'phone_number' => $this->isMethod('POST') ? 'required|unique:companies,phone_number' : ['required', Rule::unique('companies', 'phone_number')->ignore($this->route('id'))],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(response()->json([
            "status" => false,
            "message" => $validator->errors(),
        ], 400));
    }
}
