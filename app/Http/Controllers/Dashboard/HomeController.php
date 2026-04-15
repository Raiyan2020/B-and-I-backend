<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\InterestRequest;
use App\Models\InvestmentSeat;
use App\Models\Opportunity;
use App\Models\PreferredSector;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $days = collect(range(6, 0))
            ->map(fn (int $offset) => Carbon::today()->subDays($offset))
            ->values();

        $last7Days = $days->map(fn (Carbon $date) => $date->format('d/m'))->all();

        $statCards = [
            $this->buildStatCard(
                title: __('dashboard.admins'),
                link: route('admin.admins.index'),
                icon: 'feather icon-shield',
                color: 'danger',
                queryFactory: fn () => Admin::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.investors'),
                link: route('admin.investors.index'),
                icon: 'feather icon-trending-up',
                color: 'success',
                queryFactory: fn () => User::query()->where('role', UserRole::Investor->value),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.advertisers_companies'),
                link: route('admin.advertisers.index'),
                icon: 'feather icon-briefcase',
                color: 'primary',
                queryFactory: fn () => User::query()->where('role', UserRole::Advertiser->value),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.categories'),
                link: route('admin.categories.index'),
                icon: 'feather icon-list',
                color: 'warning',
                queryFactory: fn () => Category::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.opportunities_menu'),
                link: route('admin.opportunities.index'),
                icon: 'feather icon-layout',
                color: 'info',
                queryFactory: fn () => Opportunity::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.investment_seats_menu'),
                link: route('admin.investment-seats.index'),
                icon: 'feather icon-file-text',
                color: 'secondary',
                queryFactory: fn () => InvestmentSeat::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.proposed_deals'),
                link: route('admin.interest-requests.index'),
                icon: 'feather icon-git-pull-request',
                color: 'primary',
                queryFactory: fn () => InterestRequest::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.successful_deals'),
                link: route('admin.opportunities.index'),
                icon: 'feather icon-award',
                color: 'success',
                queryFactory: fn () => Opportunity::query()->where('status', OpportunityStatus::Completed->value),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.subscription_packages_menu'),
                link: route('admin.subscription_packages.index'),
                icon: 'feather icon-layers',
                color: 'danger',
                queryFactory: fn () => SubscriptionPackage::query(),
                days: $days,
            ),
            $this->buildStatCard(
                title: __('dashboard.preferred_sectors'),
                link: route('admin.preferred_sectors.index'),
                icon: 'feather icon-target',
                color: 'warning',
                queryFactory: fn () => PreferredSector::query(),
                days: $days,
            ),
        ];

        $activitySections = [
            [
                'title' => __('dashboard.recent admins'),
                'icon' => 'feather icon-shield',
                'color' => 'danger',
                'items' => Admin::query()->latest()->take(4)->get()->map(fn (Admin $admin) => [
                    'title' => $admin->name ?? __('dashboard.admin'),
                    'description' => __('dashboard.added'),
                    'time' => $admin->created_at?->diffForHumans(),
                ])->all(),
            ],
            [
                'title' => __('dashboard.recent_investors'),
                'icon' => 'feather icon-trending-up',
                'color' => 'success',
                'items' => User::query()
                    ->where('role', UserRole::Investor->value)
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(fn (User $user) => [
                        'title' => $user->name ?: __('dashboard.investor'),
                        'description' => __('dashboard.joined'),
                        'time' => $user->created_at?->diffForHumans(),
                    ])->all(),
            ],
            [
                'title' => __('dashboard.recent_advertisers_companies'),
                'icon' => 'feather icon-briefcase',
                'color' => 'primary',
                'items' => User::query()
                    ->where('role', UserRole::Advertiser->value)
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(fn (User $user) => [
                        'title' => $user->name ?: __('dashboard.advertiser'),
                        'description' => __('dashboard.joined'),
                        'time' => $user->created_at?->diffForHumans(),
                    ])->all(),
            ],
            [
                'title' => __('dashboard.latest_advertisements'),
                'icon' => 'feather icon-layout',
                'color' => 'info',
                'items' => Opportunity::query()
                    ->with(['user', 'category'])
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(fn (Opportunity $opportunity) => [
                        'title' => $opportunity->company_name ?: __('dashboard.opportunities_menu'),
                        'description' => $opportunity->category?->getTranslation('name', app()->getLocale()) ?: __('dashboard.category'),
                        'time' => $opportunity->created_at?->diffForHumans(),
                    ])->all(),
            ],
        ];

        $categoryChart = $this->categoryPerformanceChart();

        return view('dashboard.home.index', [
            'statCards' => $statCards,
            'last7Days' => $last7Days,
            'activitySections' => array_values(array_filter($activitySections, fn (array $section) => ! empty($section['items']))),
            'categoryChart' => $categoryChart,
        ]);
    }

    /**
     * @param  callable():Builder  $queryFactory
     * @param  Collection<int, Carbon>  $days
     * @return array<string, mixed>
     */
    private function buildStatCard(string $title, string $link, string $icon, string $color, callable $queryFactory, Collection $days): array
    {
        $chartData = $days->map(function (Carbon $date) use ($queryFactory): int {
            $query = $queryFactory();

            return (clone $query)
                ->whereDate('created_at', $date->toDateString())
                ->count();
        })->all();

        $todayQuery = $queryFactory();
        $countQuery = $queryFactory();

        $todayCount = (clone $todayQuery)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->count();

        return [
            'title' => $title,
            'count' => $countQuery->count(),
            'chartData' => $chartData,
            'growth' => $this->growthPercentage($queryFactory),
            'todayCount' => $todayCount,
            'link' => $link,
            'icon' => $icon,
            'color' => $color,
        ];
    }

    /**
     * @param  callable():Builder  $queryFactory
     */
    private function growthPercentage(callable $queryFactory): float
    {
        $now = Carbon::now();
        $previousQuery = $queryFactory();
        $currentQuery = $queryFactory();

        $previous = (clone $previousQuery)
            ->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])
            ->count();

        $current = (clone $currentQuery)
            ->whereBetween('created_at', [$now->copy()->subDays(7), $now])
            ->count();

        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryPerformanceChart(): array
    {
        $adsByCategory = Opportunity::query()
            ->selectRaw('category_id, COUNT(*) as total')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $seatsByCategory = InvestmentSeat::query()
            ->join('opportunities', 'opportunities.id', '=', 'investment_seats.opportunity_id')
            ->selectRaw('opportunities.category_id as category_id, COUNT(investment_seats.id) as total')
            ->whereNotNull('opportunities.category_id')
            ->groupBy('opportunities.category_id')
            ->pluck('total', 'category_id');

        $interestsByCategory = InterestRequest::query()
            ->join('opportunities', 'opportunities.id', '=', 'interest_requests.opportunity_id')
            ->selectRaw('opportunities.category_id as category_id, COUNT(interest_requests.id) as total')
            ->whereNotNull('opportunities.category_id')
            ->groupBy('opportunities.category_id')
            ->pluck('total', 'category_id');

        $categoryIds = collect($adsByCategory->keys())
            ->merge($seatsByCategory->keys())
            ->merge($interestsByCategory->keys())
            ->unique()
            ->values();

        $categories = Category::query()
            ->whereIn('id', $categoryIds)
            ->get()
            ->map(function (Category $category) use ($adsByCategory, $seatsByCategory, $interestsByCategory) {
                $ads = (int) ($adsByCategory[$category->id] ?? 0);
                $seats = (int) ($seatsByCategory[$category->id] ?? 0);
                $interests = (int) ($interestsByCategory[$category->id] ?? 0);
                $name = $category->getTranslation('name', app()->getLocale(), false)
                    ?: $category->getTranslation('name', 'ar', false)
                    ?: $category->getTranslation('name', 'en', false)
                    ?: __('dashboard.category') . ' #' . $category->id;

                return [
                    'name' => $name,
                    'ads' => $ads,
                    'seats' => $seats,
                    'interests' => $interests,
                    'score' => $ads + $seats + $interests,
                ];
            })
            ->sortByDesc('score')
            ->take(8)
            ->values();

        return [
            'labels' => $categories->pluck('name')->all(),
            'adsData' => $categories->pluck('ads')->all(),
            'seatsData' => $categories->pluck('seats')->all(),
            'interestsData' => $categories->pluck('interests')->all(),
        ];
    }
}
