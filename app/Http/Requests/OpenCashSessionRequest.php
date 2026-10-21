<?php

namespace AppHttpRequests;

use IlluminateFoundationHttpFormRequest;

class OpenCashSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cash_register_id' => ['required', 'integer', 'exists:cash_registers,id'],
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
