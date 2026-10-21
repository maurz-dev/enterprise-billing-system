<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'series' => ['required', 'string', 'size:4', 'regex:/^[FBC][0-9A-Z]{3}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'series.regex' => 'El formato de serie debe iniciar con F, B o C seguido de 3 caracteres alfanuméricos (ej: F001, B001).',
        ];
    }
}
