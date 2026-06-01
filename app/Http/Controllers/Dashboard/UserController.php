<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\InvestorExperience;
use App\Enums\InvestorType;
use App\Enums\NotificationCategory;
use App\Enums\NotificationType;
use App\Enums\OpportunityStatus;
use App\Enums\UserRole;
use App\Http\Requests\Dashboard\Users\ChargeWalletRequest;
use App\Http\Requests\Dashboard\Users\SendNotificationRequest;
use App\Http\Requests\Dashboard\Users\StoreRequest;
use App\Http\Requests\Dashboard\Users\UpdateRequest;
use App\Models\Category;
use App\Models\PreferredSector;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Services\Core\BaseService;
use App\Services\Notifications\GeneralNotificationService;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends AdminBasicController
{
    public function __construct(private readonly GeneralNotificationService $generalNotificationService)
    {
        parent::__construct(
            model: User::class,
            storeRequest: StoreRequest::class,
            updateRequest: UpdateRequest::class,
            directoryName: 'users',
            serviceName: new BaseService(User::class),
            indexScopes: 'search',
            with: [],
        );

        $this->middleware('permission:users', ['only' => ['index', 'advertisers', 'investors', 'show']]);
        $this->middleware('permission:add-user', ['only' => ['create', 'createAdvertiser', 'createInvestor', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy', 'destroyMultiple']]);
        $this->middleware('permission:block-user', ['only' => ['toggleBlock']]);
    }

    public function index(): View|JsonResponse
    {
        return $this->indexListing();
    }

    public function create(): View
    {
        return $this->createAdvertiser();
    }

    public function advertisers(): View|JsonResponse
    {
        return $this->indexListing(UserRole::Advertiser);
    }

    public function investors(): View|JsonResponse
    {
        return $this->indexListing(UserRole::Investor);
    }

    public function createAdvertiser(): View
    {
        return $this->createByRole(UserRole::Advertiser);
    }

    public function createInvestor(): View
    {
        return $this->createByRole(UserRole::Investor);
    }

    public function store(): JsonResponse|RedirectResponse
    {
        $this->storeRequest = app($this->storeRequest);
        $validated = $this->storeRequest->validated();
        $role = $this->resolveRole($validated['role'] ?? request()->input('role'));
        $validated = $this->prepareUserData($validated, $role);

        $model = $this->serviceName->create($validated);

        if (method_exists($this->serviceName, 'afterCreate')) {
            $this->serviceName->afterCreate($model, $validated);
        }

        return response()->json([
            'url' => route($this->roleContext($role)['indexRouteName']),
        ]);
    }

    public function edit($id): View
    {
        $row = $this->serviceName->find($id);

        return view('dashboard.' . $this->directoryName . '.edit', array_merge(
            ['row' => $row],
            $this->roleContext($this->resolveRole($row->role)),
            $this->roleSpecificContext($this->resolveRole($row->role)),
            $this->editCompactVariables ?? [],
        ));
    }

    public function update($id): JsonResponse|RedirectResponse
    {
        $row = $this->serviceName->find($id);
        $this->updateRequest = app($this->updateRequest);
        $validated = $this->updateRequest->validated();
        $validated = $this->prepareUserData($validated, $this->resolveRole($row->role), $row);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $this->serviceName->update(id: $id, data: $validated);

        $model = $this->serviceName->find($id);

        if (method_exists($this->serviceName, 'afterUpdate')) {
            $this->serviceName->afterUpdate($model, $validated);
        }

        return response()->json([
            'url' => route($this->roleContext($this->resolveRole($row->role))['indexRouteName']),
            'msg' => __('dashboard.item updated successfully'),
        ]);
    }

    /**
     * Toggle block status for a user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBlock($id)
    {
        try {
            $result = $this->serviceName->toggleBlock($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status (phone verification) for a user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id)
    {
        try {
            $result = $this->serviceName->toggleActive($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Override show method to load user relations for the details page.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $row = $this->serviceName->find(id: $id, with: [
                'preferredSector',
                'category',
                'wallet',
                'walletTransactions' => function ($query) {
                    $query->latest('wallet_transactions.created_at')->take(10);
                },
                'orders' => function($query) {
                    $query->latest()->take(10);
                },
                'profileUpdateRequests' => function ($query) {
                    $query->with('reviewer')->latest()->take(10);
                },
                'accountDeletionRequests' => function ($query) {
                    $query->with(['reviewer', 'user' => fn ($userQuery) => $userQuery->withTrashed()])->latest()->take(10);
                },
                'companyInvestorInterestRequestsSent' => function ($query) {
                    $query->with('investor')->latest('id')->take(15);
                },
            ]);
        } catch (ModelNotFoundException $e) {
            $fallbackUrl = url()->previous() !== url()->current()
                ? url()->previous()
                : route('admin.users.index');

            return redirect($fallbackUrl)
                ->with('error', __('dashboard.user_not_found'));
        }

        try {
            $row->loadCount('companyInvestorInterestRequestsSent');
        } catch (\Exception $e) {
            $row->setAttribute('company_investor_interest_requests_sent_count', 0);
        }

        try {
            $row->loadCount('walletTransactions');
        } catch (\Exception $e) {
            $row->setAttribute('wallet_transactions_count', 0);
        }

        try {
            $row->loadCount([
                'orders',
                'orders as completed_orders_count' => function($query) {
                    $query->where('status', 'completed');
                },
                'orders as pending_orders_count' => function($query) {
                    $query->where('status', 'pending');
                },
            ]);
        } catch (\Exception $e) {
            $row->setAttribute('orders_count', 0);
            $row->setAttribute('completed_orders_count', 0);
            $row->setAttribute('pending_orders_count', 0);
        }

        $userShowData = [
            'latestInterestRequestsByUser' => collect(),
            'latestInvestmentSeatsByUser' => collect(),
            'latestSuccessfulDealsOnAds' => collect(),
            'latestSuccessfulDealsAsInvestor' => collect(),
            'latestInvestorSuccessfulDeals' => collect(),
            'latestInvestorInvestmentSeats' => collect(),
            'latestInvestorInterestRequests' => collect(),
        ];

        if ($row->isCompany()) {
            try {
                $row->loadCount([
                    'opportunities as my_ads_count',
                    'opportunities as successful_deals_on_ads_count' => function ($query) {
                        $query->where('status', OpportunityStatus::Completed->value);
                    },
                    'awardedOpportunities as successful_deals_as_investor_count' => function ($query) use ($row) {
                        $query
                            ->where('status', OpportunityStatus::Completed->value)
                            ->where('user_id', '!=', $row->id);
                    },
                ]);
            } catch (\Exception $e) {
                $row->setAttribute('my_ads_count', 0);
                $row->setAttribute('successful_deals_on_ads_count', 0);
                $row->setAttribute('successful_deals_as_investor_count', 0);
            }

            $userShowData = [
                'latestInterestRequestsByUser' => $row->interestRequests()
                    ->with(['opportunity.user', 'investmentSeat'])
                    ->whereHas('opportunity', fn ($query) => $query->where('user_id', '!=', $row->id))
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestInvestmentSeatsByUser' => $row->investmentSeats()
                    ->with(['opportunity.user'])
                    ->whereHas('opportunity', fn ($query) => $query->where('user_id', '!=', $row->id))
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestSuccessfulDealsOnAds' => $row->opportunities()
                    ->with('investor')
                    ->where('status', OpportunityStatus::Completed->value)
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestSuccessfulDealsAsInvestor' => $row->awardedOpportunities()
                    ->with('user')
                    ->where('status', OpportunityStatus::Completed->value)
                    ->where('user_id', '!=', $row->id)
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestInvestorSuccessfulDeals' => collect(),
                'latestInvestorInvestmentSeats' => collect(),
                'latestInvestorInterestRequests' => collect(),
            ];
        } elseif ($this->resolveRole($row->role) === UserRole::Investor) {
            try {
                $row->loadCount([
                    'investmentSeats as investment_seats_count',
                    'interestRequests as interest_requests_count',
                    'awardedOpportunities as reserved_deals_count' => function ($query) {
                        $query->where('status', OpportunityStatus::Reserved->value);
                    },
                    'awardedOpportunities as successful_deals_count' => function ($query) {
                        $query->where('status', OpportunityStatus::Completed->value);
                    },
                ]);
            } catch (\Exception $e) {
                $row->setAttribute('investment_seats_count', 0);
                $row->setAttribute('interest_requests_count', 0);
                $row->setAttribute('reserved_deals_count', 0);
                $row->setAttribute('successful_deals_count', 0);
            }

            $userShowData = [
                'latestInterestRequestsByUser' => collect(),
                'latestInvestmentSeatsByUser' => collect(),
                'latestSuccessfulDealsOnAds' => collect(),
                'latestSuccessfulDealsAsInvestor' => collect(),
                'latestInvestorSuccessfulDeals' => $row->awardedOpportunities()
                    ->with('user')
                    ->where('status', OpportunityStatus::Completed->value)
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestInvestorInvestmentSeats' => $row->investmentSeats()
                    ->with(['opportunity.user'])
                    ->latest('id')
                    ->take(5)
                    ->get(),
                'latestInvestorInterestRequests' => $row->interestRequests()
                    ->with(['opportunity.user', 'investmentSeat'])
                    ->latest('id')
                    ->take(5)
                    ->get(),
            ];
        }

        return view('dashboard.' . $this->directoryName . '.show', array_merge(
            ['row' => $row],
            $userShowData,
            $this->roleContext($this->resolveRole($row->role)),
            $this->roleSpecificContext($this->resolveRole($row->role)),
        ));
    }

    /**
     * Send notification to user.
     *
     * @param SendNotificationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function sendNotification(SendNotificationRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $user = $this->serviceName->find($id);
            $validated = $request->validated();
            $payload = $validated['payload'] ?? [];

            if (! empty($validated['model_type'])) {
                $payload['model_type'] = $validated['model_type'];
            }

            if (! empty($validated['model_id'])) {
                $payload['model_id'] = $validated['model_id'];
            }

            $this->generalNotificationService->sendToUser(
                $user,
                new GeneralNotification(
                    title: [
                        'ar' => $validated['title_ar'],
                        'en' => $validated['title_en'],
                    ],
                    body: [
                        'ar' => $validated['body_ar'],
                        'en' => $validated['body_en'],
                    ],
                    notificationType: NotificationType::normalize($validated['notification_type'] ?? NotificationType::AdminNotification),
                    category: $validated['category'] ?? NotificationCategory::System,
                    payload: $payload,
                ),
            );

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'key' => 'success',
                    'msg' => __('dashboard.notification_sent_successfully'),
                ]);
            }

            return back()->with('success', __('dashboard.notification_sent_successfully'));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'key' => 'error',
                    'msg' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Charge user wallet.
     *
     * @param ChargeWalletRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function chargeWallet(ChargeWalletRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->serviceName->find($id);
            $validated = $request->validated();

            // Get or create wallet
            $wallet = $user->wallet;
            if (!$wallet || !$wallet->id) {
                $wallet = \App\Models\Wallet::firstOrCreate(
                    [
                        'walletable_type' => \App\Models\User::class,
                        'walletable_id' => $user->id,
                    ],
                    [
                        'available_balance' => 0,
                        'reserved_balance' => 0,
                    ]
                );
            }

            // Calculate new balance
            $balanceBefore = $wallet->available_balance ?? 0;
            $balanceAfter = $balanceBefore + $validated['amount'];

            // Update wallet
            $wallet->update([
                'available_balance' => $balanceAfter
            ]);

            // Refresh wallet to get updated balance
            $wallet->refresh();

            // Create transaction
            $transaction = $wallet->transactions()->create([
                'type' => \App\Enums\WalletTransactionTypeEnum::CHARGE,
                'amount' => $validated['amount'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $validated['description'] ?? __('dashboard.wallet_charge'),
            ]);

            // Load the latest transactions for response
            $latestTransactions = $wallet->transactions()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($trans) {
                    // Get type value - handle both enum object and raw value
                    if ($trans->type instanceof \App\Enums\WalletTransactionTypeEnum) {
                        $typeValue = $trans->type->value;
                    } elseif (is_object($trans->type) && isset($trans->type->value)) {
                        $typeValue = $trans->type->value;
                    } else {
                        $typeValue = (int) $trans->type;
                    }

                    $typeObj = \App\Enums\WalletTransactionTypeEnum::getFullObj($typeValue);

                    return [
                        'id' => $trans->id,
                        'type' => $typeObj['value'],
                        'type_label' => $typeObj['label'],
                        'amount' => number_format($trans->amount, 2),
                        'balance_after' => number_format($trans->balance_after, 2),
                        'description' => $trans->description,
                        'created_at' => $trans->created_at->format('Y-m-d H:i'),
                    ];
                });

            return response()->json([
                'key' => 'success',
                'msg' => __('dashboard.wallet_charged_successfully'),
                'balance' => $balanceAfter,
                'reserved_balance' => $wallet->reserved_balance ?? 0,
                'transactions' => $latestTransactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    private function indexListing(?UserRole $role = null): View|JsonResponse
    {
        $context = $role
            ? $this->roleContext($role)
            : [
                'listTitle' => __('dashboard.users list'),
                'createTitle' => __('dashboard.add user'),
                'indexRouteName' => 'admin.users.index',
                'createRouteName' => 'admin.users.create',
                'storeRouteName' => 'admin.users.store',
            ];

        if (request()->ajax()) {
            $conditions = $this->indexConditions ?? [];

            if ($role) {
                $conditions[] = ['role', '=', $role->value];
            }

            $rows = $this->serviceName->all(
                (new QueryOptions())
                    ->paginateNum(30)
                    ->scopes($this->indexScopes ?? 'search')
                    ->conditions($conditions)
                    ->with(array_merge($this->with ?? [], ['latestPendingProfileUpdateRequest', 'latestPendingAccountDeletionRequest']))
                    ->latest(true)
            );

            return DataTables::of($rows)
                ->addColumn('latest_pending_profile_update_request', function (User $user): ?array {
                    $request = $user->latestPendingProfileUpdateRequest;

                    if (! $request) {
                        return null;
                    }

                    return [
                        'id' => $request->id,
                        'status' => $request->status?->value,
                    ];
                })
                ->addColumn('latest_pending_account_deletion_request', function (User $user): ?array {
                    $request = $user->latestPendingAccountDeletionRequest;

                    if (! $request) {
                        return null;
                    }

                    return [
                        'id' => $request->id,
                        'status' => $request->status?->value,
                    ];
                })
                ->make(true);
        }

        return view(
            'dashboard.' . $this->directoryName . '.index',
            array_merge($context, $this->indexCompactVariables ?? []),
        );
    }

    private function createByRole(UserRole $role): View
    {
        return view(
            'dashboard.' . $this->directoryName . '.create',
            array_merge($this->roleContext($role), $this->roleSpecificContext($role), $this->createCompactVariables ?? []),
        );
    }

    private function roleContext(UserRole $role): array
    {
        return match ($role) {
            UserRole::Advertiser => [
                'roleValue' => $role->value,
                'listTitle' => __('dashboard.advertisers_companies_list'),
                'createTitle' => __('dashboard.add_advertiser_company'),
                'editTitle' => __('dashboard.edit_advertiser_company'),
                'detailsTitle' => __('dashboard.advertiser_company_details'),
                'indexRouteName' => 'admin.advertisers.index',
                'createRouteName' => 'admin.advertisers.create',
                'storeRouteName' => 'admin.users.store',
            ],
            UserRole::Investor => [
                'roleValue' => $role->value,
                'listTitle' => __('dashboard.investors_list'),
                'createTitle' => __('dashboard.add_investor'),
                'editTitle' => __('dashboard.edit_investor'),
                'detailsTitle' => __('dashboard.investor_details'),
                'indexRouteName' => 'admin.investors.index',
                'createRouteName' => 'admin.investors.create',
                'storeRouteName' => 'admin.users.store',
            ],
        };
    }

    private function resolveRole(UserRole|string|null $role): UserRole
    {
        if ($role instanceof UserRole) {
            return $role;
        }

        return UserRole::from($role ?: UserRole::Advertiser->value);
    }

    private function prepareUserData(array $validated, UserRole $role, ?User $user = null): array
    {
        $firstName = trim((string) ($validated['first_name'] ?? $validated['name'] ?? $user?->first_name ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? $user?->last_name ?? ''));

        if ($lastName === '') {
            $lastName = $firstName;
        }

        unset($validated['name']);

        $validated['role'] = $role->value;
        $validated['first_name'] = $firstName;
        $validated['last_name'] = $lastName;
        $validated['display_name'] = trim($firstName . ' ' . $lastName);

        if ($role === UserRole::Investor) {
            unset($validated['company_license']);

            if (array_key_exists('available_capital', $validated) && ! array_key_exists('capital', $validated)) {
                $validated['capital'] = $validated['available_capital'];
            }
        } else {
            unset(
                $validated['investor_type'],
                $validated['capital'],
                $validated['available_capital'],
                $validated['preferred_sector_id'],
                $validated['category_id'],
                $validated['experience_level'],
                $validated['previous_investments_count'],
                $validated['investor_experience'],
            );
        }

        return $validated;
    }

    private function roleSpecificContext(UserRole $role): array
    {
        if ($role !== UserRole::Investor) {
            return [];
        }

        return [
            'investorTypes' => InvestorType::cases(),
            'investorExperiences' => InvestorExperience::cases(),
            'categories' => Category::query()->where('status', true)->get(),
            'preferredSectors' => PreferredSector::query()->where('status', true)->get(),
        ];
    }
}
