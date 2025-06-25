<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(range(1, 5) as $index) {

            Category::create([
                'name' => 'Category ' . $index,
            ]);
        }

        foreach (range(1, 10) as $index) {

            Course::create([
                'title' => 'Course ' . $index,
                'description' => 'Description for course ' . $index,
                'instructor_id' => User::inRandomOrder()->first()->id, // Randomly assign an instructor
                'level' => rand(1, 3), // Assuming levels are 1, 2, and 3
                'is_published' => rand(0, 1), // Randomly published or not
                'category_id' => rand(1, 5), // Assuming you have at least 5 categories
            ]);
        }

    }
}
