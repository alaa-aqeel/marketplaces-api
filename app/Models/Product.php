<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'external_id',
        'source',
        'source_url',
        'title',
        'description',
        'price',
        'currency',
        'image',
        'image_hash',
    ];

    protected $hidden = [
        'search_vector',
        'search_rank',
        'image_hash'
    ];

    /**
     * Scope to add search condition
     */
    public function scopeWhereSearch($query, string $searchTerm)
    {
        return $query->where(function ($query) use ($searchTerm) {
            $query->whereRaw(
                "search_vector @@ plainto_tsquery('simple', ?)",
                [$searchTerm]
            );
        });
    }

    /**
     * Scope to add search ranking
     */
    public function scopeWithRankSearch($query, string $searchTerm)
    {
        return $query->selectRaw(
            "*,  ts_rank(search_vector, plainto_tsquery('simple', ?)) as search_rank",
            [$searchTerm]
        )->orderByDesc('search_rank');
    }


    /**
     * Update search vectors for existing data
     */
    public static function updateSearchVectors()
    {
        DB::statement("
            UPDATE products
            SET search_vector =
                setweight(to_tsvector('english', COALESCE(title, '')), 'A') ||
                setweight(to_tsvector('english', COALESCE(description, '')), 'B') ||
                setweight(to_tsvector('arabic', COALESCE(title, '')), 'A') ||
                setweight(to_tsvector('arabic', COALESCE(description, '')), 'B')
        ");
    }
}
