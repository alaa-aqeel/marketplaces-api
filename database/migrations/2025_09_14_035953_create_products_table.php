<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("external_id");
            $table->string("source");
            $table->string("source_url")->nullable();
            $table->string("title")->unique();
            $table->text("description")->nullable();
            $table->decimal("price", 20, 2);
            $table->string("currency", 10)->default('USD');
            $table->string("image");
            $table->text("image_hash");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
