<?php

namespace AppHttpResources;

use IlluminateHttpRequest;
use IlluminateHttpResourcesJsonJsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'product_variant_id' => $this->product_variant_id,
            'direction' => $this->direction->value,
            'reason' => $this->reason->value,
            'quantity' => (float) $this->quantity,
            'unit_cost' => (float) $this->unit_cost,
            'total_cost' => (float) $this->total_cost,
            'balance_quantity' => (float) $this->balance_quantity,
            'balance_avg_cost' => (float) $this->balance_avg_cost,
            'reference_type' => $this->reference_type,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
