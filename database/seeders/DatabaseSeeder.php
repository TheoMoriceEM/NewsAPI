<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private array $sampleData = [
        [
            'name'     => 'Belgium',
            'code'     => 'be',
            'language' => ['nl'],
        ],
        [
            'name'     => 'Canada',
            'code'     => 'ca',
            'language' => [
                'en',
                'fr',
            ],
        ],
        [
            'name'     => 'France',
            'code'     => 'fr',
            'language' => ['fr'],
        ],
        [
            'name'     => 'Germany',
            'code'     => 'de',
            'language' => ['de'],
        ],
        [
            'name'     => 'United kingdom',
            'code'     => 'gb',
            'language' => ['en'],
        ],
    ];

    private array $categories = [
        'business',
        'entertainment',
        'environment',
        'food',
        'health',
        'politics',
        'science',
        'sports',
        'technology',
        'top',
        'tourism',
        'world',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->categories as $category) {
            DB::table('categories')->insert([
                'name' => $category,
            ]);
        }

        foreach ($this->sampleData as $country) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $country['name'],
                'code' => $country['code'],
            ]);
            foreach ($country['language'] as $language) {
                $language = Language::firstOrCreate([
                    'language' => $language,
                ]);
                DB::table('country_language')->insert([
                    'country_id'  => $countryId,
                    'language_id' => $language->id,
                ]);
            }
            foreach (Category::all() as $category) {
                DB::table('category_country')->insert([
                    'category_id' => $category->id,
                    'country_id'  => $countryId,
                ]);
            }
        }
    }
}
