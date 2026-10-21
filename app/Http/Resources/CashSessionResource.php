<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'register_id' => $this->cash_register_id,
            'status' => $this->status->value,
            'opening_amount' => (float) $this->opening_amount,
            'expected_cash' => $this->expected_cash !== null ? (float) $this->expected_cash : null,
            'actual_cash' => $this->actual_cash !== null ? (float) $this->actual_cash : null,
            'difference' => $this->difference !== null ? (float) $this->difference : null,
            'opened_at' => $this->opened_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            'notes' => $this->notes,
        ];
    }
}
