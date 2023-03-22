<?php

namespace Database\Seeders;

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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
        }
    }
}
