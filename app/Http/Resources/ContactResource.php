<?php

namespace AppHttpResources;

use IlluminateHttpRequest;
use IlluminateHttpResourcesJsonJsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doc_type' => $this->doc_type,
            'doc_number' => $this->doc_number,
            'legal_name' => $this->legal_name,
            'trade_name' => $this->trade_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_customer' => (bool) $this->is_customer,
            'is_supplier' => (bool) $this->is_supplier,
        ];
    }
}
