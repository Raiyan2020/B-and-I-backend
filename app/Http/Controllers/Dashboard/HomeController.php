<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();
        $clientsCount = User::count();
        $adminsCount = Admin::count();

        // Get last 7 days data for charts
        $last7Days = [];
        $clientsData = [];
        $adminsData = [];
        $categoriesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last7Days[] = $date->format('Y-m-d');
            $clientsData[] = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $adminsData[] = Admin::whereDate('created_at', $date->format('Y-m-d'))->count();
            $categoriesData[] = Category::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // Get today's statistics
        $todayUsersCount = User::whereDate('created_at', Carbon::today())->count();
        $todayAdminsCount = Admin::whereDate('created_at', Carbon::today())->count();
        $todayCategoriesCount = Category::whereDate('created_at', Carbon::today())->count();

        // Get last 30 days for monthly trends
        $last30Days = [];
        $monthlyClientsData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last30Days[] = $date->format('d/m');
            $monthlyClientsData[] = User::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        // Get recent activity (last 10 users and admins)
        $recentUsers = User::latest()->take(5)->get();
        $recentAdmins = Admin::latest()->take(5)->get();
        $recentCategories = Category::latest()->take(5)->get();

        // Calculate growth percentages
        $lastWeekUsersCount = User::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
        $thisWeekUsersCount = User::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $usersGrowth = $lastWeekUsersCount > 0 ? round((($thisWeekUsersCount - $lastWeekUsersCount) / $lastWeekUsersCount) * 100, 1) : 0;

        $lastWeekAdminsCount = Admin::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
        $thisWeekAdminsCount = Admin::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $adminsGrowth = $lastWeekAdminsCount > 0 ? round((($thisWeekAdminsCount - $lastWeekAdminsCount) / $lastWeekAdminsCount) * 100, 1) : 0;

        $lastWeekCategoriesCount = Category::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
        $thisWeekCategoriesCount = Category::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $categoriesGrowth = $lastWeekCategoriesCount > 0 ? round((($thisWeekCategoriesCount - $lastWeekCategoriesCount) / $lastWeekCategoriesCount) * 100, 1) : 0;

        return view('dashboard.home.index', [
            'categoriesCount' => $categoriesCount,
            'clientsCount' => $clientsCount,
            'adminsCount' => $adminsCount,
            'last7Days' => $last7Days,
            'clientsData' => $clientsData,
            'adminsData' => $adminsData,
            'categoriesData' => $categoriesData,
            'todayUsersCount' => $todayUsersCount,
            'todayAdminsCount' => $todayAdminsCount,
            'todayCategoriesCount' => $todayCategoriesCount,
            'last30Days' => $last30Days,
            'monthlyClientsData' => $monthlyClientsData,
            'recentUsers' => $recentUsers,
            'recentAdmins' => $recentAdmins,
            'recentCategories' => $recentCategories,
            'usersGrowth' => $usersGrowth,
            'adminsGrowth' => $adminsGrowth,
            'categoriesGrowth' => $categoriesGrowth,
        ]);
    }
}
