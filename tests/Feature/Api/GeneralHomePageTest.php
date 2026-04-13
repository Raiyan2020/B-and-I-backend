<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Feature;
use App\Models\GeneralSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneralHomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_structured_payload(): void
    {
        GeneralSetting::insert([
            ['key' => 'website_name_ar', 'value' => 'Arabic name'],
            ['key' => 'website_name_en', 'value' => 'English name'],
            ['key' => 'project_brief_ar', 'value' => 'Arabic brief'],
            ['key' => 'project_brief_en', 'value' => 'Brief'],
            ['key' => 'logo1', 'value' => 'logo.svg'],
            ['key' => 'website_header_title_ar', 'value' => 'Arabic title'],
            ['key' => 'website_header_title_en', 'value' => 'Title'],
            ['key' => 'website_header_desc_ar', 'value' => 'Arabic desc'],
            ['key' => 'website_header_desc_en', 'value' => 'Desc'],
        ]);

        $category = Category::factory()->create([
            'status' => true,
            'parent_id' => null,
            'name' => ['ar' => 'Sector AR', 'en' => 'Sector'],
        ]);

        User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
            'password' => 'password',
        ]);

        Feature::query()->create([
            'title' => ['ar' => 'Feature AR', 'en' => 'Feature'],
            'description' => ['ar' => 'Details AR', 'en' => 'Details'],
            'image' => null,
            'status' => true,
        ]);

        $response = $this->getJson('/api/v1/general/home-page');

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonStructure([
                'data' => [
                    'website_name',
                    'project_brief',
                    'logo_url',
                    'website_header' => ['title', 'description'],
                    'features',
                    'sections',
                    'latest_opportunities',
                ],
            ]);

        $response->assertJsonPath('data.website_name', 'English name');
        $response->assertJsonPath('data.website_header.title', 'Title');
        $response->assertJsonCount(1, 'data.features');
        $response->assertJsonPath('data.sections.0.id', $category->id);
        $response->assertJsonCount(0, 'data.latest_opportunities');
    }
}
