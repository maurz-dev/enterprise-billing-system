<?php

namespace AppHttpControllers;

use AppHttpResourcesStockMovementResource;
use AppModelsProductVariant;
use AppModelsStockMovement;
use IlluminateHttpJsonResponse;
use IlluminateHttpRequest;

class StockMovementController extends Controller
{
    /**
     * Consulta el Kardex histórico de una variante de producto en un almacén.
     */
    public function kardexByVariant(Request $request, int $variantId): JsonResponse
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        $kardex = StockMovement::where('product_variant_id', $variantId)
            ->when($request->warehouse_id, fn ($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->orderBy('id', 'asc')
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'product_name' => $variant->product->name,
            ],
            'movements' => StockMovementResource::collection($kardex)->response()->getData(true),
        ]);
    }
}
