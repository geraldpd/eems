<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(config('eems.event_types') as $type) {
            Type::create([
                'name' => $type,
                'is_active' => 1
            ]);
        }
    }
}
