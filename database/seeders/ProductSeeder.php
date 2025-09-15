<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::factory(5)->create([
            "source" => "AliExpress"
        ]);
        \App\Models\Product::factory(5)->create([
            "source" => "Amazon"
        ]);
        \App\Models\Product::factory(5)->create([
            "source" => "Alibaba"
        ]);
    }
}
