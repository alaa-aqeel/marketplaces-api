<?php

namespace App\Repositories;

use App\Helper\FileUpload;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository
{

    /**
     * Filter products based on provided criteria.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(array $filters = [])
    {
        $query = Product::query();
        $query->when($filters['external_id'] ?? null, function ($q, $externalId) {
            $q->where('external_id', $externalId);
        });
        $query->when($filters['source'] ?? null, function ($q, $source) {
            $q->where('source', $source);
        });
        $query->when($filters['title'] ?? null, function ($q, $title) {
            $q->where('title', 'like', "%{$title}%");
        });

        return $query;
    }

    /**
     * Get all products with optional filtering and pagination.
     *
     * @param array $filters
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $limit = 15)
    {
        return $this->filter($filters)->paginate($limit);
    }

    /**
     * Get a product by its ID.
     *
     * @param string $id
     * @return Product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Create a new product with the given data.
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data)
    {
        /**
         *  The updated data array with the uploaded file path and its hash
         */
        FileUpload::uploadProductImage($data);

        return Product::create($data);
    }

    /**
     * Update the given product with the provided data.
     *
     * @param Product $model
     * @param array $data
     * @return bool
     */
    public function update(Product $product, array $data)
    {
        FileUpload::uploadProductImage($data);
        $product->update($data);
        $product->refresh();

        return $product;
    }

    public function upsert(array $data)
    {
        return DB::table('products')->upsert($data,
            ['external_id','source'],
            [
                'title',
                'description',
                'price',
                'currency',
                'image',
                'image_hash',
            ]
        );
    }

    /**
     * Delete the given product.
     *
     * @param Product $product
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Product $product)
    {
        return $product->delete();
    }
}
