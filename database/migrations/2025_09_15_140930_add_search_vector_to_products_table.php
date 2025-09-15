<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent;');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');

        DB::statement("ALTER TABLE products ADD COLUMN search_vector tsvector NULL");
        DB::statement("CREATE INDEX products_search_vector_idx ON products USING GIN(search_vector)");

        DB::statement("
            CREATE OR REPLACE FUNCTION products_search_vector_trigger() RETURNS trigger AS $$
            BEGIN
                -- Combined search vector with both English and Arabic
                NEW.search_vector :=
                    -- English with weight A for title, B for description
                    setweight(to_tsvector('english', COALESCE(NEW.title, '')), 'A') ||
                    setweight(to_tsvector('english', COALESCE(NEW.description, '')), 'B') ||
                    -- Arabic with weight A for title, B for description
                    setweight(to_tsvector('arabic', COALESCE(NEW.title, '')), 'A') ||
                    setweight(to_tsvector('arabic', COALESCE(NEW.description, '')), 'B');

                RETURN NEW;
            END
            $$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER products_search_vector_update
            BEFORE INSERT OR UPDATE ON products
            FOR EACH ROW EXECUTE FUNCTION products_search_vector_trigger();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            DB::statement('DROP TRIGGER IF EXISTS products_search_vector_update ON products');
            DB::statement('DROP FUNCTION IF EXISTS products_search_vector_trigger');
        });
    }
};
