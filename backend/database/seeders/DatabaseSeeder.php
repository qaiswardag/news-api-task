<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $languages = ['en', 'fr', 'de', 'nl'];
        foreach ($languages as $lang) {
            Language::firstOrCreate(['language' => $lang]);
        }

        $categories = [
            'business',
            'crime',
            'domestic',
            'education',
            'entertainment',
            'environment',
            'food',
            'health',
            'lifestyle',
            'politics',
            'science',
            'sports',
            'technology',
            'top',
            'tourism',
            'world',
            'other',
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        $countries = [
            ['name' => 'Belgium', 'code' => 'be', 'languages' => ['nl']],
            ['name' => 'Canada', 'code' => 'ca', 'languages' => ['en', 'fr']],
            ['name' => 'France', 'code' => 'fr', 'languages' => ['fr']],
            ['name' => 'Germany', 'code' => 'de', 'languages' => ['de']],
            ['name' => 'United Kingdom', 'code' => 'gb', 'languages' => ['en']],
        ];

        foreach ($countries as $data) {
            $country = Country::create([
                'name' => $data['name'],
                'code' => $data['code'],
            ]);

            // Attach languages
            $langIds = Language::whereIn('language', $data['languages'])->pluck('id')->toArray();
            $country->languages()->attach($langIds);

            // Attach random categories
            $catIds = Category::inRandomOrder()->take(3)->pluck('id')->toArray();
            $country->categories()->attach($catIds);
        }
    }
}
