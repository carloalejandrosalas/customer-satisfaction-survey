<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate surveys for each user.
        $users = User::all();
        $categories = Category::all();

        // Randomly generate 20 surveys using existing users and categories
        for ($i = 0; $i < 20; $i++) {
            Survey::factory()
                ->has(SurveyQuestion::factory()->count(10), 'questions')
                ->create([
                    'creator_id' => $users->random()->id,
                    'category_id' => $categories->random()->id,
                ]);
        }
    }
}
