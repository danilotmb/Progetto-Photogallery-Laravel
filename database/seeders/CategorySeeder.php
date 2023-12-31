<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cats = [

            'abstract',
            'animals',
            'business',
            'cats',
            'city',
            'food',
            'nightlife',
            'fashion',
            'people',
            'nature',
            'sports',
            'technics',
            'transport'
        ];

        $user_id = User::inRandomOrder()->pluck('id')->first();

        foreach ($cats as $cat) 
        {
            Category::create([
                'category_name' => $cat,
                'user_id' => $user_id
            ]);
        }
    }
}
