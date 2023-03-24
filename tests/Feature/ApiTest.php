<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_country_index_request(): void
    {
        $response = $this->getJson('/api/countries');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('countries', 5, fn(AssertableJson $json) => $json
                    ->hasAll([
                        'name',
                        'code',
                        'languages',
                        'categories',
                    ])
                    ->whereAllType([
                        'languages'  => 'array',
                        'categories' => 'array',
                    ])
                )
            );
    }

    public function test_country_show_request(): void
    {
        $response = $this->getJson('/api/country/fr');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'name',
                    'code',
                    'languages',
                    'categories',
                ])
                ->where('code', 'fr')
                ->whereAllType([
                    'languages'  => 'array',
                    'categories' => 'array',
                ])
            );
    }

    public function test_country_category_toggle_request(): void
    {
        $response = $this->postJson('/api/country/fr/business');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'attached',
                    'detached',
                ])
                ->whereAllType([
                    'attached' => 'array',
                    'detached' => 'array',
                ])
            );
    }

    public function test_latest_news_request(): void
    {
        $response = $this->getJson('/api/country/fr/fr/business');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'status',
                    'totalResults',
                    'results',
                    'nextPage',
                ])
                ->whereAllType([
                    'status'       => 'string',
                    'totalResults' => 'integer',
                    'results'      => 'array',
                    'nextPage'     => 'string',
                ])
                ->where('status', 'success')
                ->where('results.0.category', fn($categories) => $categories->contains('business'))
                ->where('results.0.country', fn($countries) => $countries->contains('france'))
                ->where('results.0.language', 'french')
            )
            ->assertJsonCount(10, 'results');
    }

    public function test_latest_news_without_category_request(): void
    {
        $response = $this->getJson('/api/country/fr/fr');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'status',
                    'totalResults',
                    'results',
                    'nextPage',
                ])
                ->whereAllType([
                    'status'       => 'string',
                    'totalResults' => 'integer',
                    'results'      => 'array',
                    'nextPage'     => 'string',
                ])
                ->where('status', 'success')
                ->where('results.0.country', fn($countries) => $countries->contains('france'))
                ->where('results.0.language', 'french')
            )
            ->assertJsonCount(10, 'results');
    }

    public function test_latest_news_first_page_request(): void
    {
        $response = $this->getJson('/api/news/fr');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'status',
                    'totalResults',
                    'results',
                    'nextPage',
                ])
                ->whereAllType([
                    'status'       => 'string',
                    'totalResults' => 'integer',
                    'results'      => 'array',
                    'nextPage'     => 'string',
                ])
                ->where('status', 'success')
                ->where('results.0.country', fn($countries) => $countries->contains('france'))
            )
            ->assertJsonCount(10, 'results');
    }

    public function test_latest_news_by_page_request(): void
    {
        $response = $this->getJson('/api/news/fr/2');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json
                ->hasAll([
                    'status',
                    'totalResults',
                    'results',
                    'nextPage',
                ])
                ->whereAllType([
                    'status'       => 'string',
                    'totalResults' => 'integer',
                    'results'      => 'array',
                    'nextPage'     => 'string',
                ])
                ->where('status', 'success')
                ->where('results.0.country', fn($countries) => $countries->contains('france'))
            )
            ->assertJsonCount(10, 'results');
    }

    public function test_latest_news_page_0_request(): void
    {
        $response = $this->getJson('/api/news/fr/0');

        $response->assertNotFound();
    }
}
