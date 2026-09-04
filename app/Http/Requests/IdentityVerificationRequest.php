<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class IdentityVerificationRequest extends FormRequest
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
            'legal_first_name' => ['required', 'string', 'max:255'],
            'legal_last_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', new Enum(DocumentType::class)],
            'document_number' => ['required', 'string', 'max:100'],
            'document_expiry_date' => ['required', 'date', 'after:today'],
            'front_photo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'back_photo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }
}
