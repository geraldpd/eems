<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Category::factory()
        //->count(50)
        //->create();

        foreach(['Business', 'Government', 'IT'] as $category) {
            Category::create([
                'name' => $category,
                'is_active' => 1
            ]);
        }
    }
}
