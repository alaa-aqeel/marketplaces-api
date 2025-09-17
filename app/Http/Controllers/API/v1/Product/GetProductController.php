<?php

namespace App\Http\Controllers\API\v1\Product;

use App\Http\Controllers\Controller;
use App\Jobs\InsertProductsFromMarketplace;
use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Exception;
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
        $products = Cache::tags(["product", "all"])->remember(
            "products:search:$q:limit:$limit:page:$page", 60,
            function() use($limit, $q, $page) {
                try {
                    return $this->service->fetchAllProducts($q, $page, $limit);
                } catch(Exception $e) {
                    Cache::tags(["prdouct", "all"])->flush();
                    abort(response()->json([
                        "message" => $e->getMessage()
                    ]), 400);
                }
            }
        );

        return response()->json($products);
    }
}
