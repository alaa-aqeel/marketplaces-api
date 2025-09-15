<?php


namespace App\Marketplaces;
use App\Interfaces\MarketplaceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

abstract class BaseMarketplace implements MarketplaceInterface {

    protected array $config;

    protected string $apiUrl;

    protected function hashImage(string|null $imageUrl)
    {
        if (is_null($imageUrl)) {
            return null;
        }
        return md5(file_get_contents($imageUrl));
    }

    function __construct(array $config = [])
    {
        $this->config = $config;
        $this->apiUrl = $config['api_url'];
    }

    abstract public function mapper($product);

    protected function client() {
        return Http::withHeaders([
            // 'Authorization' => 'Bearer ' . ($this->config['api_key'] ?? ''),
            'Accept' => 'application/json',
        ]);
    }

    protected function fetch(string $path, $parmaters = null, string|null $jsonPath = null): array {
        $response = $this->client()->get("$this->apiUrl/$path", $parmaters);
        if ($response->failed()) {
            throw new \Exception($response->status());
        }
        $data = $response->json($jsonPath);
        if (empty($data) || is_null($data)) {
            return [];
        }
        if (Arr::isAssoc($data)) {
            return $this->mapper($data);
        }

        return collect($data)
                ->map(fn($it)=> $this->mapper($it))
                ->toArray();
    }
}
