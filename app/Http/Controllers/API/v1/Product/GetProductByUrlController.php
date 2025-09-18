<?php

namespace App\Http\Controllers\API\v1\Product;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        try{
            $product = Cache::tags(["product", "id"])->remember("product:url:".$request->get("url"), 40, function () use($request) {
                return $this->marketplaceService->getProductFromUrl($request->get("url"));
            });
        } catch(Exception $e) {
            Cache::tags(["product", "id"])->flush();
            Log::error('Failed to fetch product from url: '.$request->get("url"), [
                $e->getMessage()
            ]);
            abort(response()->json([
                "error" => $e->getMessage()
            ], 400));
        }

        return response()->json($product);
    }
}
