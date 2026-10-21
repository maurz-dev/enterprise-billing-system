<?php

namespace AppHttpResources;

use IlluminateHttpRequest;
use IlluminateHttpResourcesJsonJsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'correlative' => $this->full_correlative,
            'doc_type' => [
                'code' => $this->doc_type->value,
                'name' => $this->doc_type->label(),
            ],
            'status' => $this->status->value,
            'payment_condition' => $this->payment_condition->value,
            'dates' => [
                'issue_date' => $this->issue_date?->format('Y-m-d'),
                'due_date' => $this->due_date?->format('Y-m-d'),
                'issued_at' => $this->issued_at?->toISOString(),
            ],
            'currency' => $this->currency,
            'financials' => [
                'subtotal' => (float) $this->subtotal,
                'discount_total' => (float) $this->discount_total,
                'tax_total' => (float) $this->tax_total,
                'total' => (float) $this->total,
                'paid_total' => (float) $this->paid_total,
                'pending_amount' => (float) $this->pending_amount,
            ],
            'customer' => new ContactResource($this->whenLoaded('customer')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'notes' => $this->notes,
        ];
    }
}
