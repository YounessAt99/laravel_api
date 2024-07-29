<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        \App\Models\User::factory()->create([
            'name' => 'client',
            'email' => 'client@example.com',
        ]);

        Category::factory(5)->create();
        Product::factory(10)->create();
    }
}
