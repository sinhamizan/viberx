<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'state' => ['required', 'string', 'in:'.implode(',', array_keys(config('states')))],
        ];
    }

    /**
     * Additional validation: the chosen state must be available for treatment.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $state = $this->input('state');

            if ($state && ! (config("states.{$state}.available") ?? false)) {
                $validator->errors()->add('state', 'Treatment is not yet available in this state.');
            }
        });
    }
}
