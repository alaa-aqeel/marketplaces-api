<?php

namespace App\Http\Controllers\API\v1\Product;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetProductByUrlController extends Controller
{
    function __construct(
        private MarketplaceService $marketplaceService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            "url" => "url|string",
        ]);
        // $product = Cache::remember("product:url:".$request->get("url"), 60, function () use($request) {

            $product = $this->marketplaceService->getProductFromUrl($request->get("url"));
        // });

        return response()->json($product);
    }
}
