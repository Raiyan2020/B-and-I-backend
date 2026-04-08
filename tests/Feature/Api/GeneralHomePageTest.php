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
            ['key' => 'website_name_ar', 'value' => 'اسم عربي'],
            ['key' => 'website_name_en', 'value' => 'English name'],
            ['key' => 'project_brief_ar', 'value' => 'نبذة'],
            ['key' => 'project_brief_en', 'value' => 'Brief'],
            ['key' => 'logo1', 'value' => 'logo.svg'],
            ['key' => 'website_header_title_ar', 'value' => 'عنوان'],
            ['key' => 'website_header_title_en', 'value' => 'Title'],
            ['key' => 'website_header_desc_ar', 'value' => 'وصف'],
            ['key' => 'website_header_desc_en', 'value' => 'Desc'],
        ]);

        $category = Category::factory()->create([
            'status' => true,
            'parent_id' => null,
            'name' => ['ar' => 'قطاع', 'en' => 'Sector'],
        ]);

        User::factory()->create([
            'role' => UserRole::Advertiser,
            'category_id' => $category->id,
            'password' => 'password',
        ]);

        Feature::query()->create([
            'title' => ['ar' => 'مزية', 'en' => 'Feature'],
            'description' => ['ar' => 'تفاصيل', 'en' => 'Details'],
            'image' => null,
            'status' => true,
        ]);

        $response = $this->getJson('/api/v1/general/home-page');

        $response->assertOk()
            ->assertJsonPath('key', 'success')
            ->assertJsonStructure([
                'data' => [
                    'branding' => ['website_name', 'project_brief', 'logo_url'],
                    'hero' => ['title', 'description'],
                    'value_propositions',
                    'sectors',
                    'latest_opportunities',
                ],
            ]);

        $response->assertJsonPath('data.branding.website_name.ar', 'اسم عربي');
        $response->assertJsonPath('data.hero.title.en', 'Title');
        $response->assertJsonCount(1, 'data.value_propositions');
        $response->assertJsonPath('data.sectors.0.items_count', 1);
        $response->assertJsonCount(0, 'data.latest_opportunities');
    }
}
