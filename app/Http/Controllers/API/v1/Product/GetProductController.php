<?php

namespace App\Http\Controllers\API\v1\Product;

use App\Http\Controllers\Controller;
use App\Jobs\InsertProductsFromMarketplace;
use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetProductController extends Controller
{
    private string $source = "alibaba" ;
    private int $limit = 2;

    function __construct(private ProductRepository $productRepository)
    {}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $products = Cache::remember(
            "products:search:".$request->get("q").":limit:".$request->get("limit", 10), 60,
            function () use($request) {
                return $this->productRepository->getAll([
                    "search" => $request->get("q"),
                ], $request->get("limit", 10));
            }
        );

        return response()->json($products);
    }
}
