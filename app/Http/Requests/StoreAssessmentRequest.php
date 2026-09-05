<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
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
            'personal_info.date_of_birth' => ['required', 'date', 'before:today'],
            'personal_info.sex' => ['required', 'in:male,female,other'],
            'personal_info.height_in' => ['required', 'numeric', 'min:1'],
            'personal_info.weight_lb' => ['required', 'numeric', 'min:1'],
            'personal_info.address_line1' => ['required', 'string', 'max:255'],
            'personal_info.city' => ['required', 'string', 'max:255'],
            'personal_info.state' => ['required', 'string', 'max:255'],
            'personal_info.postal_code' => ['required', 'string', 'max:20'],

            'medical_history.notes' => ['required', 'string', 'max:2000'],
            'medications.notes' => ['required', 'string', 'max:2000'],
            'allergies.notes' => ['required', 'string', 'max:2000'],
            'prior_treatments.notes' => ['required', 'string', 'max:2000'],

            'health_conditions.conditions' => ['required', 'array', 'min:1'],
            'health_conditions.conditions.*' => [
                'string',
                'in:diabetes,hypertension,heart_disease,liver_or_kidney_disease,pregnant_or_breastfeeding,mental_health_condition,none',
            ],

            'goals.notes' => ['required', 'string', 'max:2000'],
        ];
    }
}
