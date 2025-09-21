<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            [
                'name' => 'Food & Dining',
                'color' => '#EF4444',
                'description' => 'Restaurants, groceries, food delivery, and dining out'
            ],
            [
                'name' => 'Transportation',
                'color' => '#3B82F6',
                'description' => 'Gas, public transport, ride sharing, car maintenance'
            ],
            [
                'name' => 'Shopping',
                'color' => '#8B5CF6',
                'description' => 'Clothing, electronics, books, general shopping'
            ],
            [
                'name' => 'Entertainment',
                'color' => '#F59E0B',
                'description' => 'Movies, concerts, games, hobbies, streaming services'
            ],
            [
                'name' => 'Bills & Utilities',
                'color' => '#10B981',
                'description' => 'Electricity, water, internet, phone, rent'
            ],
            [
                'name' => 'Healthcare',
                'color' => '#EC4899',
                'description' => 'Medical appointments, pharmacy, insurance, wellness'
            ],
            [
                'name' => 'Education',
                'color' => '#06B6D4',
                'description' => 'Courses, books, training, workshops'
            ],
            [
                'name' => 'Travel',
                'color' => '#84CC16',
                'description' => 'Hotels, flights, vacation expenses, travel insurance'
            ],
            [
                'name' => 'Personal Care',
                'color' => '#F43F5E',
                'description' => 'Haircuts, cosmetics, spa, personal grooming'
            ],
            [
                'name' => 'Other',
                'color' => '#6B7280',
                'description' => 'Miscellaneous expenses that don\'t fit other categories'
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
