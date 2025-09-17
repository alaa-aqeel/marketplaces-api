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

    function __construct(
        private MarketplaceService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $limit = $request->get("limit", 10);
        $q = $request->get("q", '');
        $page = $request->get("page", 1);
        $products = Cache::remember(
            "products:search:$q:limit:$limit:page:$page", 60,
            fn() => $this->service->fetchAllProducts($q, $page, $limit)
        );

        return response()->json($products);
    }
}
