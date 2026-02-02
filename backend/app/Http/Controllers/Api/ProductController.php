<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $cacheKey = 'products:list:q=' . mb_strtolower($q);

        $products = Cache::remember($cacheKey, 60, function () use ($q) {
            $query = Product::query()->select(['id', 'title', 'price', 'image']);

            if ($q !== '') {
                $query->where('title', 'like', '%' . $q . '%');
            }

            return $query->orderBy('id', 'desc')->get();
        });

        return response()->json($products);
    }

    public function show(int $id)
    {
        $cacheKey = 'products:by_id:' . $id;

        $product = Cache::remember($cacheKey, 60, function () use ($id) {
            return Product::query()->findOrFail($id);
        });

        return response()->json($product);
    }
}
