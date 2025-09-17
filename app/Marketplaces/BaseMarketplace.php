<?php


namespace App\Marketplaces;
use App\Interfaces\MarketplaceInterface;
use Closure;
use GuzzleHttp\Promise\Promise;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

abstract class BaseMarketplace implements MarketplaceInterface {

    protected array $config;

    protected string $apiUrl;

    private PendingRequest|Pool $http;

    function __construct(array $config = [])
    {
        $this->config = $config;
        $this->apiUrl = $config['api_url'];

        $this->http = Http::acceptJson()->timeout(5);
    }

    abstract public function mapper($product);

    protected function hashImage(string|null $imageUrl)
    {
        if (is_null($imageUrl)) {
            return null;
        }
        return md5(file_get_contents($imageUrl));
    }

    public static function extractProductId(string $url)
    {
        return null;
    }

    public function withPool($pool)
    {
        $this->http = $pool;

        return $this;
    }

    protected function mapperResponse(Response $response, string|null $jsonPath = null)
    {
        $data = $response->json($jsonPath);
        if (empty($data) || is_null($data)) {
            return [];
        }
        if (Arr::isAssoc($data)) {
            return $this->mapper($data);
        }
        return array_map(fn($it) => $this->mapper($it), $data);
    }

    protected function fetch(string $path, array $parmaters = []): Response|Promise {

        Log::debug($this->config["name"]."[REQUEST]: Send reqeust to GET $this->apiUrl/$path", $parmaters);
        return $this->http->get("$this->apiUrl/$path", $parmaters);
    }
}
