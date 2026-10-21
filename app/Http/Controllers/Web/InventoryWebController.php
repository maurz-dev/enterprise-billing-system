<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryWebController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = Category::all();

        $query = ProductVariant::with(['product.category', 'product.unit', 'stocks.warehouse'])
            ->where('is_active', true)
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->whereHas('product', fn ($p) => $p->where('category_id', $categoryId));
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            });

        $variants = $query->paginate(15)->withQueryString();

        $recentMovements = StockMovement::with(['variant.product', 'warehouse'])
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $totalStockUnits = Stock::sum('quantity');
        $allActive = ProductVariant::with('stocks')->where('is_active', true)->get();
        $totalVariantsCount = $allActive->count();
        $lowStockCount = $allActive->filter(function ($v) {
            return $v->stocks->sum('quantity') <= $v->min_stock;
        })->count();

        return view('inventory.index', compact(
            'variants',
            'warehouses',
            'categories',
            'recentMovements',
            'totalStockUnits',
            'totalVariantsCount',
            'lowStockCount',
            'warehouseId',
            'categoryId',
            'search'
        ));
    }
}
