<?php

namespace App\Http\Controllers\Api\V1\General;

use App\Enums\UserRole;
use App\Facades\BaseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FeaturesResource;
use App\Http\Resources\PublicOpportunityResource;
use App\Http\Resources\WhoWeAreResource;
use App\Models\AboutUsItem;
use App\Models\Category;
use App\Models\Feature;
use App\Models\GeneralSetting;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\Devices\DeviceService;
use App\Support\QueryOptions;
use App\Traits\ResponseTrait;
use Faker\Provider\Base;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use ResponseTrait;

    public function __construct(private readonly DeviceService $deviceService)
    {
    }

    /**
     * Change application language (ar/en only)
     */
    public function changeLang(Request $request): JsonResponse
    {
        $lang = strtolower(substr((string)(
            $request->input('lang')
            ?? $request->headers->get('Accept-Language')
            ?? $request->query('lang')
        ), 0, 2));

        // Validate language - only ar or en allowed
        if (!in_array($lang, ['ar', 'en'])) {
            return $this->jsonResponse(
                msg: __('api.invalid_language'),
                code: 400,
                key: 'fail'
            );
        }

        app()->setLocale($lang);

        /** @var User|null $user */
        $user = auth('sanctum')->user();
        if ($user instanceof User) {
            $user->update(['lang' => $lang]);

            if ($request->filled('device_token')) {
                $this->deviceService->updateUserDeviceLocale($user, $request->string('device_token')->toString(), $lang);
            }
        }

        $langName = $lang === 'ar' ? __('api.language_arabic') : __('api.language_english');

        return $this->jsonResponse(
            msg: __('api.language_changed', ['language' => $langName]),
            data: ['lang' => $lang]
        );
    }

    public function whoWeAre(): JsonResponse
    {
        $lang = app()->getLocale() ?? request()->headers->get('Accept-Language', 'en');
        $whoWeAre = GeneralSetting::where('key', 'like', "about_us_%_{$lang}")->get();
        $whoWeAreSettings = [
            'title'       => $whoWeAre->firstWhere('key', "about_us_title_{$lang}")?->value,
            'description' => $whoWeAre->firstWhere('key', "about_us_description_{$lang}")?->value,
        ];
        $options = (new QueryOptions())->conditions(['status' => true])->latest();
        $whoWeAreItems = BaseService::setModel(AboutUsItem::class)->all($options);

        return $this->jsonResponse(data: [
            'whoWeAreSettings' => $whoWeAreSettings,
            'items'            => WhoWeAreResource::collection($whoWeAreItems),
        ]);
    }

    /**
     * Aggregates landing-page content: branding, hero, value propositions (features),
     * sectors (root categories + counts), and latest opportunities (reserved for listings).
     */
    public function homePage(): JsonResponse
    {
        $lang = app()->getLocale() ?? request()->headers->get('Accept-Language', 'en');
        $logo = GeneralSetting::getValueForKey('logo1');
        $websiteName = GeneralSetting::getValueForKey("website_name_{$lang}");
        $projectBrief = GeneralSetting::getValueForKey("project_brief_{$lang}");
        $websiteHeaderTitle = GeneralSetting::getValueForKey("website_header_title_{$lang}");
        $websiteHeaderDesc = GeneralSetting::getValueForKey("website_header_desc_{$lang}");
        $ourFeatures = BaseService::setModel(Feature::class)->all((new QueryOptions())->conditions(['status' => true])->latest());
        $sections = BaseService::setModel(Category::class)
            ->limit(
                (new QueryOptions())
                    ->latest()
                    ->conditions(['status' => true])
                    ->withCount(['opportunities'])
            );
        $data = [
            'website_name'         => $websiteName ?? '',
            'project_brief'        => $projectBrief ?? '',
            'logo_url'             => $logo ? asset('Site/assets/images/logo/' . $logo) : null,
            'website_header'       => [
                'title'       => $websiteHeaderTitle ?? '',
                'description' => $websiteHeaderDesc ?? '',
            ],
            'features'             => FeaturesResource::collection($ourFeatures),
            'sections'             => CategoryResource::collection($sections), //$sections,
            'latest_opportunities' => PublicOpportunityResource::collection(
                Opportunity::query()->with(['category', 'user'])->where('status', 'approved')->latest()->limit(6)->get()
            ),
        ];
        return $this->jsonResponse(data: $data);
    }

    public function privacyPolicy(): JsonResponse
    {
        $lang = app()->getLocale() ?? request()->headers->get('Accept-Language', 'ar');
        $key = "privacy_policy_{$lang}";
        return $this->jsonResponse(data: GeneralSetting::getValueForKey($key));
    }

    public function termsAndConditions(): JsonResponse
    {
        $lang = app()->getLocale() ?? request()->headers->get('Accept-Language', 'ar');
        $key = "terms_{$lang}";
        return $this->jsonResponse(data: GeneralSetting::getValueForKey($key));
    }
}
