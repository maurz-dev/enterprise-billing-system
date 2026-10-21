<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseCashSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'denominations' => ['nullable', 'array'],
            'denominations.*.denomination' => ['required_with:denominations', 'numeric', 'min:0.1'],
            'denominations.*.quantity' => ['required_with:denominations', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
