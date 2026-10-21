<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCashMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'reason' => ['required', 'string', 'min:3', 'max:200'],
        ];
    }
}
