<?php

namespace App\Helper;



class CatchProductId
{
    public function aliexpress()
    {

    }

    public static function catchId(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (strpos($host, 'aliexpress.com') !== false) {
            preg_match('/item\/(\d+)\.html/', $url, $matches);
            return ['source' => 'aliexpress', 'id' => $matches[1] ?? null];
        }
        if (strpos($host, 'alibaba.com') !== false) {
            preg_match('/detail\/(\d+)\.html/', $url, $matches);
            return ['source' => 'alibaba', 'id' => $matches[1] ?? null];
        }

        return ['source' => null, 'id' => null];
    }
}
