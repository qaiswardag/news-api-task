<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\Country;
use App\Models\Category;
use App\Models\Language;

class NewsControllerTest extends TestCase
{
	use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();
		// Seed a country, category, and language for testing
		$country = Country::factory()->create(['code' => 'ca', 'name' => 'Canada']);
		$category = Category::factory()->create(['name' => 'sports']);
		$language = Language::factory()->create(['language' => 'en']);
		$country->categories()->attach($category);
		$country->languages()->attach($language);
	}

	public function test_news_index_success()
	{
		Http::fake([
			'*' => Http::response([
				'results' => [
					['title' => 'Test News', 'description' => 'Test Description']
				]
			], 200)
		]);

		$response = $this->getJson('/api/v1/news?country=ca&language=en&category=sports');
		$response->assertStatus(200)
			->assertJsonStructure([
				'data' => [
					['title', 'description']
				]
			]);
	}

	public function test_news_index_validation_error()
	{
		$response = $this->getJson('/api/v1/news?country=ca');
		$response->assertStatus(422);
	}

	public function test_news_show_country_not_found()
	{
		$response = $this->getJson('/api/v1/news/xx');
		$response->assertStatus(404)
			->assertJson(['message' => 'Country not found']);
	}
}
