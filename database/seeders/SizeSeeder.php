<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sizes;

class SizeSeeder extends Seeder
{
    public function run()
    {
        Sizes::create(['name' => 'Small']);
        Sizes::create(['name' => 'Medium']);
        Sizes::create(['name' => 'Large']);
        Sizes::create(['name' => 'XL']);
    }
}