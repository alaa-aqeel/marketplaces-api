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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("payment_id");
            $table->string("gateway");
            $table->string("status");
            $table->string("currency");
            $table->jsonb("details");
            $table->decimal("amount", 20, 2);
            $table->foreignUuid("order_id")
                ->constrained("orders")
                ->cascadeOnDelete();
            $table->timestamps();

            $table->index(['gateway', 'status']);
            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
