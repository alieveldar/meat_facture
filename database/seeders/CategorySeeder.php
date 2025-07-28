<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class CategorySeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('categories')->delete();

        Category::factory()->count(5)->create();
    }
}
