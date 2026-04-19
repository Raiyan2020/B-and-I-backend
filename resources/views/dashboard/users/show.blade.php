<x-dashboard.layouts.master title="{{ $detailsTitle ?? __('dashboard.customer details') }}">
    @php
        $isInvestor = ($roleValue ?? null) === \App\Enums\UserRole::Investor->value;
        $isCompany = $row->isCompany();
        $notSpecified = __('dashboard.not specified');
        $hasCustomImage = filled($row->getRawOriginal('image')) && $row->getRawOriginal('image') !== 'default.png';
        $languageLabel = match ($row->lang) {
            'ar' => __('api.language_arabic'),
            'en' => __('api.language_english'),
            default => $notSpecified,
        };
        $createdAtLabel = $row->created_at ? $row->created_at->format('Y-m-d H:i') : $notSpecified;
        $country = $row->country_code ? \App\Helpers\CountryHelper::getCountryByCode($row->country_code) : null;
        $countryIso = strtolower($country['iso'] ?? 'sa');
        $profileUpdateCount = $row->profileUpdateRequests?->count() ?? 0;
        $accountDeletionCount = $row->accountDeletionRequests?->count() ?? 0;
        $companyInterestCount = $row->company_investor_interest_requests_sent_count ?? 0;
        $myAdsCount = (int) ($row->my_ads_count ?? 0);
        $successfulDealsOnAdsCount = (int) ($row->successful_deals_on_ads_count ?? 0);
        $successfulDealsAsInvestorCount = (int) ($row->successful_deals_as_investor_count ?? 0);
        $investmentSeatsCount = (int) ($row->investment_seats_count ?? 0);
        $interestRequestsCount = (int) ($row->interest_requests_count ?? 0);
        $reservedDealsCount = (int) ($row->reserved_deals_count ?? 0);
        $successfulDealsCount = (int) ($row->successful_deals_count ?? 0);
        $latestInterestRequestsByUser = $latestInterestRequestsByUser ?? collect();
        $latestInvestmentSeatsByUser = $latestInvestmentSeatsByUser ?? collect();
        $latestSuccessfulDealsOnAds = $latestSuccessfulDealsOnAds ?? collect();
        $latestSuccessfulDealsAsInvestor = $latestSuccessfulDealsAsInvestor ?? collect();
        $latestInvestorSuccessfulDeals = $latestInvestorSuccessfulDeals ?? collect();
        $latestInvestorInvestmentSeats = $latestInvestorInvestmentSeats ?? collect();
        $latestInvestorInterestRequests = $latestInvestorInterestRequests ?? collect();
        $walletTransactions = $row->walletTransactions ?? collect();
        $walletTransactionsCount = $row->wallet_transactions_count ?? $walletTransactions->count();
        $fullPhone = $row->full_phone ?: $notSpecified;
        $displayValue = static fn($value) => filled($value) ? $value : $notSpecified;
        $companyLicenseAvailable = filled($row->company_license_url);
        $roleLabel = $isInvestor ? __('dashboard.investor') : __('dashboard.advertiser');
        $roleSpecificTitle = $isInvestor
            ? __('dashboard.investor_details')
            : __('dashboard.advertiser_company_details');

        $heroFacts = [
            [
                'icon' => 'mail',
                'label' => __('dashboard.table email'),
                'value' => $displayValue($row->email),
                'tone' => 'info',
            ],
            [
                'icon' => 'phone',
                'label' => __('dashboard.table phone'),
                'value' => $fullPhone,
                'tone' => 'success',
                'flag' => $row->country_code ? $countryIso : null,
            ],
            [
                'icon' => 'globe',
                'label' => __('dashboard.table language'),
                'value' => $languageLabel,
                'tone' => 'warning',
            ],
            $isInvestor
                ? [
                    'icon' => 'briefcase',
                    'label' => __('dashboard.investor_type'),
                    'value' => $row->investor_type
                        ? __('enums.investor_type.' . ($row->investor_type->value ?? $row->investor_type))
                        : $notSpecified,
                    'tone' => 'primary',
                ]
                : [
                    'icon' => 'file-text',
                    'label' => __('dashboard.table company license'),
                    'value' => $companyLicenseAvailable ? __('dashboard.view current license') : $notSpecified,
                    'tone' => 'primary',
                    'is_link' => true,
                    'href' => $companyLicenseAvailable ? $row->company_license_url : null,
                ],
        ];

        $infoGroups = [
            [
                'title' => __('dashboard.personal information'),
                'items' => [
                    [
                        'icon' => 'user',
                        'label' => __('dashboard.table name'),
                        'value' => $displayValue($row->name),
                        'tone' => 'primary',
                    ],
                    [
                        'icon' => 'user-plus',
                        'label' => __('dashboard.table first name'),
                        'value' => $displayValue($row->first_name),
                        'tone' => 'info',
                    ],
                    [
                        'icon' => 'users',
                        'label' => __('dashboard.table last name'),
                        'value' => $displayValue($row->last_name),
                        'tone' => 'secondary',
                    ],
                    [
                        'icon' => 'calendar',
                        'label' => __('dashboard.created at'),
                        'value' => $createdAtLabel,
                        'tone' => 'muted',
                    ],
                ],
            ],
            [
                'title' => __('dashboard.user details'),
                'items' => [
                    [
                        'icon' => 'mail',
                        'label' => __('dashboard.table email'),
                        'value' => $displayValue($row->email),
                        'tone' => 'info',
                    ],
                    [
                        'icon' => 'phone',
                        'label' => __('dashboard.table phone'),
                        'value' => $fullPhone,
                        'tone' => 'success',
                        'flag' => $row->country_code ? $countryIso : null,
                    ],
                    [
                        'icon' => 'check-circle',
                        'label' => __('dashboard.account_status'),
                        'value' => $row->is_active ? __('dashboard.active') : __('dashboard.inactive'),
                        'tone' => $row->is_active ? 'success' : 'warning',
                    ],
                    [
                        'icon' => 'slash',
                        'label' => __('dashboard.block_status'),
                        'value' => $row->is_blocked ? __('dashboard.blocked') : __('dashboard.un_blocked'),
                        'tone' => $row->is_blocked ? 'danger' : 'success',
                    ],
                    [
                        'icon' => 'shield',
                        'label' => __('dashboard.email_verification_status'),
                        'value' => $row->email_verified ? __('dashboard.verified') : __('dashboard.not_verified'),
                        'tone' => $row->email_verified ? 'success' : 'warning',
                    ],
                    [
                        'icon' => 'globe',
                        'label' => __('dashboard.table language'),
                        'value' => $languageLabel,
                        'tone' => 'warning',
                    ],
                ],
            ],
            [
                'title' => $roleSpecificTitle,
                'items' => $isInvestor
                    ? [
                        [
                            'icon' => 'briefcase',
                            'label' => __('dashboard.investor_type'),
                            'value' => $row->investor_type
                                ? __('enums.investor_type.' . ($row->investor_type->value ?? $row->investor_type))
                                : $notSpecified,
                            'tone' => 'primary',
                        ],
                        [
                            'icon' => 'dollar-sign',
                            'label' => __('dashboard.capital'),
                            'value' => $displayValue($row->capital),
                            'tone' => 'success',
                        ],
                        [
                            'icon' => 'credit-card',
                            'label' => __('dashboard.available_capital'),
                            'value' => $displayValue($row->available_capital),
                            'tone' => 'warning',
                        ],
                        [
                            'icon' => 'target',
                            'label' => __('dashboard.preferred_sectors'),
                            'value' =>
                                $row->preferredSector?->getTranslation('name', app()->getLocale()) ?? $notSpecified,
                            'tone' => 'info',
                        ],
                        [
                            'icon' => 'layers',
                            'label' => __('dashboard.category'),
                            'value' => $row->category?->getTranslation('name', app()->getLocale()) ?? $notSpecified,
                            'tone' => 'secondary',
                        ],
                        [
                            'icon' => 'award',
                            'label' => __('dashboard.investor_experience'),
                            'value' => $row->investor_experience
                                ? __(
                                    'enums.investor_experience.' .
                                        ($row->investor_experience->value ?? $row->investor_experience),
                                )
                                : $notSpecified,
                            'tone' => 'danger',
                        ],
                        [
                            'icon' => 'trending-up',
                            'label' => __('dashboard.experience_level'),
                            'value' => $displayValue($row->experience_level),
                            'tone' => 'primary',
                        ],
                        [
                            'icon' => 'bar-chart-2',
                            'label' => __('dashboard.previous_investments_count'),
                            'value' => $displayValue($row->previous_investments_count),
                            'tone' => 'info',
                        ],
                    ]
                    : [
                        [
                            'icon' => 'file-text',
                            'label' => __('dashboard.table company license'),
                            'value' => $companyLicenseAvailable ? __('dashboard.view current license') : $notSpecified,
                            'tone' => 'primary',
                            'is_link' => true,
                            'href' => $companyLicenseAvailable ? $row->company_license_url : null,
                        ],
                        [
                            'icon' => 'user-check',
                            'label' => __('dashboard.company_investor_interest_requests_menu'),
                            'value' => $companyInterestCount,
                            'tone' => 'info',
                        ],
                    ],
            ],
        ];

        if ($isCompany) {
            $stats = [
                [
                    'label' => __('dashboard.investor_outreach_requests'),
                    'value' => $companyInterestCount,
                    'icon' => 'user-plus',
                    'tone' => 'secondary',
                ],
                [
                    'label' => __('dashboard.total_advertisements'),
                    'value' => $myAdsCount,
                    'icon' => 'shopping-bag',
                    'tone' => 'primary',
                ],
                [
                    'label' => __('dashboard.successful_deals_on_ads'),
                    'value' => $successfulDealsOnAdsCount,
                    'icon' => 'check-circle',
                    'tone' => 'success',
                ],
                [
                    'label' => __('dashboard.successful_deals_as_investor'),
                    'value' => $successfulDealsAsInvestorCount,
                    'icon' => 'award',
                    'tone' => 'warning',
                ],
                [
                    'label' => __('dashboard.profile_update_requests'),
                    'value' => $profileUpdateCount,
                    'icon' => 'refresh-cw',
                    'tone' => 'info',
                ],
                [
                    'label' => __('dashboard.account_deletion_requests'),
                    'value' => $accountDeletionCount,
                    'icon' => 'trash-2',
                    'tone' => 'danger',
                ],
            ];
        } elseif ($isInvestor) {
            $stats = [
                [
                    'label' => __('dashboard.investment_seats_count'),
                    'value' => $investmentSeatsCount,
                    'icon' => 'shopping-cart',
                    'tone' => 'primary',
                ],
                [
                    'label' => __('dashboard.interest_requests_count'),
                    'value' => $interestRequestsCount,
                    'icon' => 'send',
                    'tone' => 'info',
                ],
                [
                    'label' => __('dashboard.reserved_deals_for_investor'),
                    'value' => $reservedDealsCount,
                    'icon' => 'clock',
                    'tone' => 'warning',
                ],
                [
                    'label' => __('dashboard.successful_deals'),
                    'value' => $successfulDealsCount,
                    'icon' => 'award',
                    'tone' => 'success',
                ],
                [
                    'label' => __('dashboard.profile_update_requests'),
                    'value' => $profileUpdateCount,
                    'icon' => 'refresh-cw',
                    'tone' => 'secondary',
                ],
                [
                    'label' => __('dashboard.account_deletion_requests'),
                    'value' => $accountDeletionCount,
                    'icon' => 'trash-2',
                    'tone' => 'danger',
                ],
            ];
        } else {
            $stats = [
                [
                    'label' => __('dashboard.total_orders'),
                    'value' => $row->orders_count ?? 0,
                    'icon' => 'shopping-cart',
                    'tone' => 'primary',
                ],
                [
                    'label' => __('dashboard.completed_orders'),
                    'value' => $row->completed_orders_count ?? 0,
                    'icon' => 'check-circle',
                    'tone' => 'success',
                ],
                [
                    'label' => __('dashboard.pending_orders'),
                    'value' => $row->pending_orders_count ?? 0,
                    'icon' => 'clock',
                    'tone' => 'warning',
                ],
                [
                    'label' => __('dashboard.profile_update_requests'),
                    'value' => $profileUpdateCount,
                    'icon' => 'refresh-cw',
                    'tone' => 'info',
                ],
                [
                    'label' => __('dashboard.account_deletion_requests'),
                    'value' => $accountDeletionCount,
                    'icon' => 'trash-2',
                    'tone' => 'danger',
                ],
            ];
        }
    @endphp

    <div class="app-content content user-show-page">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>

            <x-dashboard.layouts.breadcrumb now="{{ $detailsTitle ?? __('dashboard.customer details') }}">
                <li class="breadcrumb-item">
                    <a href="{{ route($indexRouteName ?? 'admin.users.index') }}">
                        {{ $listTitle ?? __('dashboard.users list') }}
                    </a>
                </li>
            </x-dashboard.layouts.breadcrumb>

            <div class="content-body">
                <section class="page-users-view user-profile-show">
                    <div class="row">
                        <div class="col-12">
                            <div class="card profile-hero-card">
                                <div class="card-body p-0">
                                    <div class="profile-hero-shell">
                                        <div class="profile-hero-top">
                                            <div>
                                                <span class="profile-role-chip">
                                                    <i
                                                        class="feather icon-{{ $isInvestor ? 'user' : 'briefcase' }}"></i>
                                                    {{ $roleLabel }}
                                                </span>
                                                <h2 class="profile-hero-title mb-50">{{ $row->name ?: $notSpecified }}
                                                </h2>
                                                <p class="profile-hero-subtitle mb-0">
                                                    {{ $detailsTitle ?? __('dashboard.customer details') }}
                                                    <span class="profile-dot"></span>
                                                    #{{ $row->id }}
                                                </p>
                                            </div>

                                            <div class="profile-hero-actions">
                                                @can('edit-user')
                                                    <a href="{{ route('admin.users.edit', $row->id) }}"
                                                        class="btn profile-btn profile-btn-primary">
                                                        <i class="feather icon-edit"></i>
                                                        <span>{{ __('dashboard.edit') }}</span>
                                                    </a>
                                                @endcan

                                                <a href="{{ route($indexRouteName ?? 'admin.users.index') }}"
                                                    class="btn profile-btn profile-btn-light">
                                                    <i class="feather icon-arrow-right"></i>
                                                    <span>{{ __('dashboard.back') }}</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row no-gutters">
                                            <div class="col-12 col-xl-3">
                                                <div class="profile-hero-aside">
                                                    <div class="profile-avatar-wrap">
                                                        @if ($hasCustomImage)
                                                            <img src="{{ $row->image }}" alt="User Avatar"
                                                                class="profile-avatar-image">
                                                        @else
                                                            <div class="profile-avatar-placeholder">
                                                                <i
                                                                    class="feather icon-{{ $isInvestor ? 'user' : 'briefcase' }}"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="profile-status-list">
                                                        <span
                                                            class="profile-status-pill profile-status-pill-{{ $row->is_active ? 'success' : 'warning' }}">
                                                            <i class="feather icon-check-circle"></i>
                                                            {{ __('dashboard.account_status') }}:
                                                            {{ $row->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                                                        </span>

                                                        <span
                                                            class="profile-status-pill profile-status-pill-{{ $row->is_blocked ? 'danger' : 'success' }}">
                                                            <i class="feather icon-slash"></i>
                                                            {{ __('dashboard.block_status') }}:
                                                            {{ $row->is_blocked ? __('dashboard.blocked') : __('dashboard.un_blocked') }}
                                                        </span>

                                                        <span
                                                            class="profile-status-pill profile-status-pill-{{ $row->email_verified ? 'info' : 'secondary' }}">
                                                            <i class="feather icon-mail"></i>
                                                            {{ __('dashboard.email_verification_status') }}:
                                                            {{ $row->email_verified ? __('dashboard.verified') : __('dashboard.not_verified') }}
                                                        </span>
                                                    </div>

                                                    <div class="profile-action-grid">
                                                        <button type="button"
                                                            class="btn profile-action-btn profile-action-btn-primary"
                                                            data-toggle="modal" data-target="#sendNotificationModal">
                                                            <i class="feather icon-bell"></i>
                                                            <span>{{ __('dashboard.send_notification') }}</span>
                                                        </button>

                                                        <button type="button"
                                                            class="btn profile-action-btn profile-action-btn-success toggle-active-btn-show"
                                                            data-url="{{ route('admin.users.toggleActive', $row->id) }}"
                                                            data-active="{{ $row->is_active ? '1' : '0' }}">
                                                            <i
                                                                class="feather icon-{{ $row->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                                            <span>{{ $row->is_active ? __('dashboard.deactivate') : __('dashboard.activate') }}</span>
                                                        </button>

                                                        @can('block-user')
                                                            <button type="button"
                                                                class="btn profile-action-btn profile-action-btn-warning toggle-block-btn-show"
                                                                data-url="{{ route('admin.users.toggleBlock', $row->id) }}"
                                                                data-blocked="{{ $row->is_blocked ? '1' : '0' }}">
                                                                <i class="feather icon-slash"></i>
                                                                <span>{{ $row->is_blocked ? __('dashboard.un_block') : __('dashboard.block') }}</span>
                                                            </button>
                                                        @endcan

                                                        @can('delete-user')
                                                            <button type="button"
                                                                class="btn profile-action-btn profile-action-btn-danger delete-row-show"
                                                                data-url="{{ route('admin.users.destroy', $row->id) }}">
                                                                <i class="feather icon-trash-2"></i>
                                                                <span>{{ __('dashboard.delete') }}</span>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-xl-9">
                                                <div class="profile-hero-main">
                                                    <div class="row">
                                                        @foreach ($heroFacts as $fact)
                                                            <div class="col-12 col-md-6">
                                                                <div class="profile-fact-card">
                                                                    <div
                                                                        class="profile-fact-icon profile-tone-{{ $fact['tone'] }}">
                                                                        <i
                                                                            class="feather icon-{{ $fact['icon'] }}"></i>
                                                                    </div>
                                                                    <div class="profile-fact-content">
                                                                        <span
                                                                            class="profile-fact-label">{{ $fact['label'] }}</span>
                                                                        <div class="profile-fact-value">
                                                                            @if (!empty($fact['flag']))
                                                                                <span
                                                                                    class="flag-icon flag-icon-{{ $fact['flag'] }} flag-icon-squared mr-50"></span>
                                                                            @endif

                                                                            @if (!empty($fact['is_link']) && !empty($fact['href']))
                                                                                <a href="{{ $fact['href'] }}"
                                                                                    target="_blank">{{ $fact['value'] }}</a>
                                                                            @else
                                                                                <span>{{ $fact['value'] }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="row">
                                                        @foreach ($infoGroups as $group)
                                                            <div class="col-12">
                                                                <div class="profile-info-panel">
                                                                    <div class="profile-info-panel-header">
                                                                        <h5 class="mb-0">{{ $group['title'] }}</h5>
                                                                    </div>

                                                                    <div class="profile-detail-grid">
                                                                        @foreach ($group['items'] as $item)
                                                                            <div class="profile-detail-tile">
                                                                                <div
                                                                                    class="profile-detail-icon profile-tone-{{ $item['tone'] ?? 'primary' }}">
                                                                                    <i
                                                                                        class="feather icon-{{ $item['icon'] }}"></i>
                                                                                </div>

                                                                                <div class="profile-detail-content">
                                                                                    <span
                                                                                        class="profile-detail-label">{{ $item['label'] }}</span>

                                                                                    <div class="profile-detail-value">
                                                                                        @if (!empty($item['flag']))
                                                                                            <span
                                                                                                class="flag-icon flag-icon-{{ $item['flag'] }} flag-icon-squared mr-50"></span>
                                                                                        @endif

                                                                                        @if (!empty($item['is_link']) && !empty($item['href']))
                                                                                            <a href="{{ $item['href'] }}"
                                                                                                target="_blank">{{ $item['value'] }}</a>
                                                                                        @else
                                                                                            <span>{{ $item['value'] }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-12">
                            <div class="card profile-section-card profile-stats-card">
                                <div class="card-header border-0 pb-0">
                                    <h4 class="card-title mb-0">
                                        <i class="feather icon-bar-chart-2 text-info mr-1"></i>
                                        {{ __('dashboard.statistics') }}
                                    </h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($stats as $stat)
                                            <div class="col-12 col-sm-6 col-lg-2 mb-1">
                                                <div class="profile-stat-card profile-tone-{{ $stat['tone'] }}">
                                                    <div class="profile-stat-icon">
                                                        <i class="feather icon-{{ $stat['icon'] }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="profile-stat-value">{{ $stat['value'] }}</div>
                                                        <div class="profile-stat-label">{{ $stat['label'] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($isCompany)
                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-send text-primary mr-1"></i>
                                                {{ __('dashboard.latest_interest_requests_by_user') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestInterestRequestsByUser->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestInterestRequestsByUser->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.submitted_at') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestInterestRequestsByUser as $interestRequest)
                                                            <tr>
                                                                <td>#{{ $interestRequest->id }}</td>
                                                                <td>{{ $interestRequest->opportunity?->opportunity_number ? '#' . $interestRequest->opportunity->opportunity_number : '-' }}
                                                                </td>
                                                                <td>{{ $interestRequest->opportunity?->company_name ?? '-' }}
                                                                </td>
                                                                <td>{{ $interestRequest->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.interest-requests.show', $interestRequest) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_interest_requests_by_user') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-shopping-cart text-success mr-1"></i>
                                                {{ __('dashboard.latest_investment_seats_by_user') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestInvestmentSeatsByUser->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestInvestmentSeatsByUser->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.purchased_at') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestInvestmentSeatsByUser as $seat)
                                                            <tr>
                                                                <td>#{{ $seat->id }}</td>
                                                                <td>{{ $seat->opportunity?->opportunity_number ? '#' . $seat->opportunity->opportunity_number : '-' }}
                                                                </td>
                                                                <td>{{ $seat->opportunity?->company_name ?? '-' }}</td>
                                                                <td>{{ $seat->purchased_at?->format('Y-m-d H:i') ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.investment-seats.show', $seat) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_investment_seats_by_user') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-check-circle text-success mr-1"></i>
                                                {{ __('dashboard.latest_successful_deals_on_ads') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestSuccessfulDealsOnAds->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestSuccessfulDealsOnAds->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.investor') }}</th>
                                                            <th>{{ __('dashboard.deal_date') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestSuccessfulDealsOnAds as $opportunity)
                                                            <tr>
                                                                <td>#{{ $opportunity->id }}</td>
                                                                <td>{{ $opportunity->opportunity_number ? '#' . $opportunity->opportunity_number : $opportunity->company_name ?? '-' }}
                                                                </td>
                                                                <td>{{ $opportunity->investor?->name ?? '-' }}</td>
                                                                <td>{{ $opportunity->reviewed_at?->format('Y-m-d H:i') ?? ($opportunity->updated_at?->format('Y-m-d H:i') ?? '-') }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.opportunities.show', $opportunity) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_successful_deals_on_ads') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-award text-warning mr-1"></i>
                                                {{ __('dashboard.latest_successful_deals_as_investor') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestSuccessfulDealsAsInvestor->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestSuccessfulDealsAsInvestor->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.deal_date') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestSuccessfulDealsAsInvestor as $opportunity)
                                                            <tr>
                                                                <td>#{{ $opportunity->id }}</td>
                                                                <td>{{ $opportunity->opportunity_number ? '#' . $opportunity->opportunity_number : $opportunity->company_name ?? '-' }}
                                                                </td>
                                                                <td>{{ $opportunity->company_name ?? ($opportunity->user?->name ?? '-') }}
                                                                </td>
                                                                <td>{{ $opportunity->reviewed_at?->format('Y-m-d H:i') ?? ($opportunity->updated_at?->format('Y-m-d H:i') ?? '-') }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.opportunities.show', $opportunity) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_successful_deals_as_investor') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($isInvestor)
                            <div class="col-12">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-award text-success mr-1"></i>
                                                {{ __('dashboard.successful_deals') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $successfulDealsCount }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestInvestorSuccessfulDeals->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.investment_required') }}</th>
                                                            <th>{{ __('dashboard.deal_date') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestInvestorSuccessfulDeals as $opportunity)
                                                            <tr>
                                                                <td>#{{ $opportunity->id }}</td>
                                                                <td>{{ $opportunity->opportunity_number ? '#' . $opportunity->opportunity_number : ($opportunity->company_name ?? '-') }}
                                                                </td>
                                                                <td>{{ $opportunity->company_name ?? ($opportunity->user?->name ?? '-') }}
                                                                </td>
                                                                <td>{{ $opportunity->investment_required ?? '-' }}</td>
                                                                <td>{{ $opportunity->reviewed_at?->format('Y-m-d H:i') ?? ($opportunity->updated_at?->format('Y-m-d H:i') ?? '-') }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.opportunities.show', $opportunity) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_successful_deals_as_investor') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-shopping-cart text-primary mr-1"></i>
                                                {{ __('dashboard.latest_investment_seats_by_user') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestInvestorInvestmentSeats->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestInvestorInvestmentSeats->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.price_paid') }}</th>
                                                            <th>{{ __('dashboard.purchased_at') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestInvestorInvestmentSeats as $seat)
                                                            <tr>
                                                                <td>#{{ $seat->id }}</td>
                                                                <td>{{ $seat->opportunity?->opportunity_number ? '#' . $seat->opportunity->opportunity_number : '-' }}
                                                                </td>
                                                                <td>{{ $seat->opportunity?->company_name ?? '-' }}</td>
                                                                <td>{{ number_format((float) ($seat->price_paid ?? 0), 2) }}
                                                                </td>
                                                                <td>{{ $seat->purchased_at?->format('Y-m-d H:i') ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.investment-seats.show', $seat) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_investment_seats_by_user') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-xl-6">
                                <div class="card profile-section-card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                            <h4 class="card-title mb-0">
                                                <i class="feather icon-send text-info mr-1"></i>
                                                {{ __('dashboard.latest_interest_requests_by_user') }}
                                            </h4>
                                            <span
                                                class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $latestInvestorInterestRequests->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        @if ($latestInvestorInterestRequests->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover profile-table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('dashboard.opportunity_reference') }}</th>
                                                            <th>{{ __('dashboard.company_name') }}</th>
                                                            <th>{{ __('dashboard.seat_reference') }}</th>
                                                            <th>{{ __('dashboard.submitted_at') }}</th>
                                                            <th>{{ __('dashboard.actions') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($latestInvestorInterestRequests as $interestRequest)
                                                            <tr>
                                                                <td>#{{ $interestRequest->id }}</td>
                                                                <td>{{ $interestRequest->opportunity?->opportunity_number ? '#' . $interestRequest->opportunity->opportunity_number : '-' }}
                                                                </td>
                                                                <td>{{ $interestRequest->opportunity?->company_name ?? '-' }}
                                                                </td>
                                                                <td>#{{ $interestRequest->investment_seat_id }}</td>
                                                                <td>{{ $interestRequest->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('admin.interest-requests.show', $interestRequest) }}"
                                                                        class="btn btn-sm btn-outline-info"
                                                                        title="{{ __('dashboard.show') }}">
                                                                        <i class="feather icon-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="profile-empty-state">
                                                <i class="feather icon-inbox"></i>
                                                <p>{{ __('dashboard.no_interest_requests_by_user') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <div class="card profile-section-card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                        <h4 class="card-title mb-0">
                                            <i class="feather icon-refresh-cw text-warning mr-1"></i>
                                            {{ __('dashboard.profile_update_requests') }}
                                        </h4>

                                        <span
                                            class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $profileUpdateCount }}</span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if (isset($row->profileUpdateRequests) && $row->profileUpdateRequests->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover profile-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('dashboard.request_status') }}</th>
                                                        <th>{{ __('dashboard.request_submitted_at') }}</th>
                                                        <th>{{ __('dashboard.reviewed_at') }}</th>
                                                        <th>{{ __('dashboard.reviewed_by') }}</th>
                                                        <th>{{ __('dashboard.rejection_reason') }}</th>
                                                        <th>{{ __('dashboard.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($row->profileUpdateRequests as $profileUpdateRequest)
                                                        <tr>
                                                            <td>#{{ $profileUpdateRequest->id }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-{{ match ($profileUpdateRequest->status?->value) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        default => 'warning',
                                                                    } }}">
                                                                    {{ __('dashboard.' . ($profileUpdateRequest->status?->value ?? 'pending')) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $profileUpdateRequest->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td>{{ $profileUpdateRequest->reviewed_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td>{{ $profileUpdateRequest->reviewer?->name ?? '-' }}
                                                            </td>
                                                            <td>{{ $profileUpdateRequest->rejection_reason ?: '-' }}
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.profile-update-requests.show', $profileUpdateRequest) }}"
                                                                    class="btn btn-sm btn-outline-info"
                                                                    title="{{ __('dashboard.show') }}">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="profile-empty-state">
                                            <i class="feather icon-inbox"></i>
                                            <p>{{ __('dashboard.no_profile_update_requests') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card profile-section-card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                        <h4 class="card-title mb-0">
                                            <i class="feather icon-trash-2 text-danger mr-1"></i>
                                            {{ __('dashboard.account_deletion_requests') }}
                                        </h4>

                                        <span
                                            class="badge badge-pill badge-primary mt-1 mt-sm-0">{{ $accountDeletionCount }}</span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if (isset($row->accountDeletionRequests) && $row->accountDeletionRequests->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover profile-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('dashboard.request_status') }}</th>
                                                        <th>{{ __('dashboard.request_submitted_at') }}</th>
                                                        <th>{{ __('dashboard.reviewed_at') }}</th>
                                                        <th>{{ __('dashboard.reviewed_by') }}</th>
                                                        <th>{{ __('dashboard.rejection_reason') }}</th>
                                                        <th>{{ __('dashboard.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($row->accountDeletionRequests as $accountDeletionRequest)
                                                        <tr>
                                                            <td>#{{ $accountDeletionRequest->id }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge badge-{{ match ($accountDeletionRequest->status?->value) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        default => 'warning',
                                                                    } }}">
                                                                    {{ __('dashboard.' . ($accountDeletionRequest->status?->value ?? 'pending')) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $accountDeletionRequest->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td>{{ $accountDeletionRequest->reviewed_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td>{{ $accountDeletionRequest->reviewer?->name ?? '-' }}
                                                            </td>
                                                            <td>{{ $accountDeletionRequest->rejection_reason ?: '-' }}
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.account-deletion-requests.show', $accountDeletionRequest) }}"
                                                                    class="btn btn-sm btn-outline-info"
                                                                    title="{{ __('dashboard.show') }}">
                                                                    <i class="feather icon-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="profile-empty-state">
                                            <i class="feather icon-inbox"></i>
                                            <p>{{ __('dashboard.no_account_deletion_requests') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('vendor-styles')
        <link rel="stylesheet" type="text/css"
            href="{{ asset('dashboardAssets/app-assets/fonts/flag-icon-css/css/flag-icon.min.css') }}">
    @endpush

    @push('page-styles')
        <style>
            .user-profile-show {
                --profile-bg: linear-gradient(135deg, #f7f9fc 0%, #eef2f7 55%, #e9eff8 100%);
                --profile-card-bg: rgba(255, 255, 255, 0.96);
                --profile-panel-bg: rgba(255, 255, 255, 0.94);
                --profile-soft-bg: #f5f7fb;
                --profile-aside-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(244, 247, 252, 0.98) 100%);
                --profile-border: rgba(148, 163, 184, 0.28);
                --profile-divider: rgba(148, 163, 184, 0.22);
                --profile-text: #24324d;
                --profile-title: #13203b;
                --profile-muted: #667085;
                --profile-link: #4f5bd5;
                --profile-chip-bg: rgba(79, 91, 213, 0.12);
                --profile-chip-text: #4451c4;
                --profile-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
                --profile-avatar-border: rgba(255, 255, 255, 0.88);
                --profile-empty-icon: #94a3b8;
                --profile-btn-light-bg: rgba(255, 255, 255, 0.96);
                --profile-btn-light-text: #23314b;
                --profile-btn-light-border: rgba(148, 163, 184, 0.25);
            }

            body.dark-layout .user-profile-show,
            body.semi-dark-layout .user-profile-show {
                --profile-bg: linear-gradient(145deg, #121a2b 0%, #0f1724 56%, #132033 100%);
                --profile-card-bg: rgba(16, 24, 39, 0.96);
                --profile-panel-bg: rgba(18, 28, 45, 0.92);
                --profile-soft-bg: rgba(30, 41, 59, 0.82);
                --profile-aside-bg: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(21, 32, 50, 0.98) 100%);
                --profile-border: rgba(100, 116, 139, 0.34);
                --profile-divider: rgba(100, 116, 139, 0.28);
                --profile-text: #dbe4f3;
                --profile-title: #f8fbff;
                --profile-muted: #9aa9c2;
                --profile-link: #9ab0ff;
                --profile-chip-bg: rgba(129, 140, 248, 0.18);
                --profile-chip-text: #c7d2fe;
                --profile-shadow: 0 22px 42px rgba(2, 6, 23, 0.38);
                --profile-avatar-border: rgba(30, 41, 59, 0.92);
                --profile-empty-icon: #64748b;
                --profile-btn-light-bg: rgba(30, 41, 59, 0.92);
                --profile-btn-light-text: #e2e8f0;
                --profile-btn-light-border: rgba(100, 116, 139, 0.32);
            }

            .user-profile-show .card {
                border: 1px solid var(--profile-border);
                border-radius: 24px;
                box-shadow: var(--profile-shadow);
                overflow: hidden;
                background: var(--profile-card-bg);
            }

            .user-profile-show .card-header {
                border-bottom: 1px solid var(--profile-divider);
                padding: 1.35rem 1.5rem 1rem;
            }

            .profile-hero-card {
                background: transparent;
            }

            .profile-hero-shell {
                position: relative;
                background: radial-gradient(circle at top left, rgba(99, 102, 241, 0.12), transparent 28%),
                    radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.1), transparent 24%),
                    var(--profile-bg);
                color: var(--profile-text);
            }

            .profile-hero-shell::before,
            .profile-hero-shell::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                pointer-events: none;
            }

            .profile-hero-shell::before {
                width: 240px;
                height: 240px;
                top: -90px;
                right: -60px;
                background: rgba(99, 102, 241, 0.08);
            }

            .profile-hero-shell::after {
                width: 160px;
                height: 160px;
                bottom: -50px;
                left: -40px;
                background: rgba(14, 165, 233, 0.08);
            }

            .profile-hero-top {
                position: relative;
                z-index: 1;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1.25rem;
                padding: 1.75rem 2rem 0;
            }

            .profile-role-chip {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.5rem 0.9rem;
                background: var(--profile-chip-bg);
                color: var(--profile-chip-text);
                font-weight: 600;
                font-size: 0.85rem;
                margin-bottom: 0.9rem;
            }

            .profile-hero-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--profile-title);
            }

            .profile-hero-subtitle {
                color: var(--profile-muted);
                font-size: 0.96rem;
            }

            .profile-dot {
                display: inline-block;
                width: 5px;
                height: 5px;
                margin: 0 0.45rem;
                border-radius: 999px;
                background: var(--profile-muted);
                vertical-align: middle;
            }

            .profile-hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .profile-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                border-radius: 14px;
                padding: 0.8rem 1rem;
                font-weight: 600;
                border: 0;
            }

            .profile-btn-primary {
                color: #fff;
                background: linear-gradient(135deg, #655dff 0%, #7f68ff 100%);
                box-shadow: 0 14px 24px rgba(101, 93, 255, 0.28);
            }

            .profile-btn-light {
                background: var(--profile-btn-light-bg);
                color: var(--profile-btn-light-text);
                border: 1px solid var(--profile-btn-light-border);
            }

            .profile-hero-aside {
                position: relative;
                z-index: 1;
                height: 100%;
                padding: 2rem 1.5rem;
                background: var(--profile-aside-bg);
                border-inline-end: 1px solid var(--profile-divider);
            }

            .profile-avatar-wrap {
                display: flex;
                justify-content: center;
                margin-bottom: 1.4rem;
            }

            .profile-avatar-image,
            .profile-avatar-placeholder {
                width: 180px;
                height: 180px;
                border-radius: 36px;
                object-fit: cover;
                box-shadow: 0 24px 50px rgba(44, 55, 109, 0.18);
                border: 8px solid var(--profile-avatar-border);
            }

            .profile-avatar-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 4rem;
                color: #fff;
                background: linear-gradient(135deg, #5f63ff 0%, #18c9bc 100%);
            }

            .profile-status-list {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.35rem;
            }

            .profile-status-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                border-radius: 999px;
                padding: 0.7rem 0.95rem;
                font-size: 0.84rem;
                font-weight: 600;
                line-height: 1.3;
                background: var(--profile-soft-bg);
            }

            .profile-status-pill-success {
                background: rgba(34, 197, 94, 0.14);
                color: #17834a;
            }

            .profile-status-pill-warning {
                background: rgba(245, 158, 11, 0.14);
                color: #b36a00;
            }

            .profile-status-pill-danger {
                background: rgba(239, 68, 68, 0.14);
                color: #b42323;
            }

            .profile-status-pill-info {
                background: rgba(14, 165, 233, 0.14);
                color: #0369a1;
            }

            .profile-status-pill-secondary {
                background: rgba(100, 116, 139, 0.14);
                color: #475569;
            }

            .profile-action-grid {
                display: grid;
                gap: 0.85rem;
            }

            .profile-action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.6rem;
                width: 100%;
                padding: 0.9rem 1rem;
                border-radius: 16px;
                font-weight: 600;
                border: 0;
                color: #fff;
            }

            .profile-action-btn-primary {
                background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            }

            .profile-action-btn-success {
                background: linear-gradient(135deg, #15803d 0%, #0f766e 100%);
            }

            .profile-action-btn-warning {
                background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
            }

            .profile-action-btn-danger {
                background: linear-gradient(135deg, #b91c1c 0%, #be185d 100%);
            }

            .profile-hero-main {
                position: relative;
                z-index: 1;
                padding: 2rem;
            }

            .profile-hero-main>.row+.row {
                margin-top: 0.35rem;
            }

            .profile-fact-card,
            .profile-info-panel,
            .profile-stat-card {
                border: 1px solid var(--profile-border);
                background: var(--profile-panel-bg);
                border-radius: 20px;
            }

            .profile-fact-card {
                display: flex;
                align-items: center;
                gap: 1rem;
                height: 100%;
                padding: 1.1rem 1.2rem;
                margin-bottom: 1.15rem;
                box-shadow: 0 12px 25px rgba(30, 41, 102, 0.08);
            }

            .profile-fact-icon,
            .profile-detail-icon,
            .profile-stat-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                flex: 0 0 auto;
            }

            .profile-fact-label,
            .profile-detail-label,
            .profile-stat-label {
                display: block;
                color: var(--profile-muted);
                font-size: 0.82rem;
                margin-bottom: 0.3rem;
            }

            .profile-fact-value,
            .profile-detail-value {
                color: var(--profile-title);
                font-weight: 700;
                word-break: break-word;
            }

            .profile-fact-value a,
            .profile-detail-value a {
                color: var(--profile-link);
            }

            .profile-info-panel {
                padding: 1.35rem 1.4rem;
                margin-bottom: 1.15rem;
                box-shadow: 0 12px 25px rgba(30, 41, 102, 0.08);
            }

            .profile-info-panel-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.15rem;
            }

            .profile-info-panel-header h5 {
                font-size: 1rem;
                font-weight: 700;
                color: var(--profile-title);
            }

            .profile-detail-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }

            .profile-detail-tile {
                display: flex;
                align-items: flex-start;
                gap: 0.9rem;
                padding: 1.05rem 1rem;
                border-radius: 18px;
                background: var(--profile-soft-bg);
                border: 1px solid var(--profile-divider);
            }

            .profile-detail-content {
                min-width: 0;
            }

            .profile-stats-card .card-body {
                padding-top: 1rem;
            }

            .profile-wallet-card {
                height: calc(100% - 1rem);
            }

            .profile-wallet-card .card-body {
                display: flex;
                min-height: 170px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .profile-wallet-balance {
                color: var(--profile-title);
                font-size: 2.2rem;
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 0.75rem;
            }

            .profile-wallet-balance span {
                color: var(--profile-muted);
                font-size: 1rem;
                font-weight: 700;
            }

            .profile-wallet-reserved {
                color: var(--profile-muted);
                font-size: 0.88rem;
            }

            .profile-stat-card {
                height: 100%;
                display: flex;
                align-items: center;
                gap: 0.9rem;
                padding: 1rem;
                margin-bottom: 1rem;
                box-shadow: 0 12px 24px rgba(30, 41, 102, 0.08);
            }

            .profile-stat-value {
                color: var(--profile-title);
                font-weight: 800;
                font-size: 1.45rem;
                line-height: 1.1;
            }

            .profile-section-card .card-title,
            .user-profile-show .card-title {
                color: var(--profile-title);
            }

            .profile-section-card .card-body {
                padding-top: 1rem;
            }

            .profile-table thead th {
                color: var(--profile-muted);
                font-size: 0.8rem;
                font-weight: 700;
                border-top: 0;
                white-space: nowrap;
            }

            .profile-table tbody td {
                color: var(--profile-text);
                vertical-align: middle;
                border-color: var(--profile-divider);
            }

            .profile-empty-state {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.85rem;
                min-height: 180px;
                text-align: center;
                color: var(--profile-muted);
            }

            .profile-empty-state i {
                font-size: 2.6rem;
                color: var(--profile-empty-icon);
            }

            .profile-tone-primary {
                background: rgba(101, 93, 255, 0.12);
                color: #5d56f3;
            }

            .profile-tone-info {
                background: rgba(14, 165, 233, 0.12);
                color: #0284c7;
            }

            .profile-tone-success {
                background: rgba(34, 197, 94, 0.12);
                color: #16a34a;
            }

            .profile-tone-warning {
                background: rgba(245, 158, 11, 0.14);
                color: #c07c08;
            }

            .profile-tone-danger {
                background: rgba(239, 68, 68, 0.12);
                color: #dc2626;
            }

            .profile-tone-secondary {
                background: rgba(100, 116, 139, 0.12);
                color: #475569;
            }

            .profile-tone-muted {
                background: rgba(148, 163, 184, 0.14);
                color: #64748b;
            }

            @media (max-width: 1199.98px) {
                .profile-hero-aside {
                    border-inline-end: 0;
                    border-bottom: 1px solid var(--profile-divider);
                }
            }

            @media (max-width: 991.98px) {
                .profile-hero-top {
                    flex-direction: column;
                }

                .profile-hero-actions {
                    width: 100%;
                }

                .profile-btn {
                    width: 100%;
                    justify-content: center;
                }
            }

            @media (max-width: 767.98px) {

                .profile-hero-top,
                .profile-hero-main,
                .profile-hero-aside {
                    padding: 1.2rem;
                }

                .profile-hero-title {
                    font-size: 1.55rem;
                }

                .profile-avatar-image,
                .profile-avatar-placeholder {
                    width: 140px;
                    height: 140px;
                    border-radius: 28px;
                }

                .profile-detail-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (min-width: 1200px) {
                .profile-hero-shell .row.no-gutters {
                    align-items: stretch;
                }

                .profile-hero-main .row:first-child {
                    margin-bottom: 0.35rem;
                }
            }
        </style>
    @endpush

    @push('page-scripts')
        <script>
            function showActionAlert(options) {
                return Swal.fire({
                    title: '{{ __('dashboard.confirm') }}',
                    text: options.text,
                    icon: options.icon || 'question',
                    showCancelButton: true,
                    confirmButtonColor: options.confirmColor || '#3085d6',
                    cancelButtonColor: options.cancelColor || '#d33',
                    confirmButtonText: '{{ __('dashboard.yes') }}',
                    cancelButtonText: '{{ __('dashboard.cancel') }}'
                });
            }

            function showActionError(message) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('dashboard.error') }}',
                    text: message || '{{ __('dashboard.something_went_wrong') }}'
                });
            }

            function showActionSuccess(message, callback) {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('dashboard.success') }}',
                    text: message,
                    timer: 1800,
                    showConfirmButton: false
                }).then(callback);
            }

            function performShowRequest(config) {
                $.ajax({
                    url: config.url,
                    type: config.method || 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.key === 'success') {
                            showActionSuccess(response.msg, config.onSuccess || function() {
                                location.reload();
                            });
                        } else {
                            showActionError(response.msg);
                        }
                    },
                    error: function(xhr) {
                        showActionError(xhr.responseJSON?.msg);
                    }
                });
            }

            $('#charge-wallet-form').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $submitButton = $('#charge-submit-btn');
                const $spinner = $submitButton.find('.spinner-border');

                $submitButton.prop('disabled', true);
                $spinner.removeClass('d-none');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        if (response.key !== 'success') {
                            showActionError(response.msg);
                            return;
                        }

                        $('#wallet-balance-display').html(response.balance +
                            ' <span>{{ __('dashboard.currency') }}</span>');
                        $('#current-balance-value').text(response.balance +
                            ' {{ __('dashboard.currency') }}');
                        $('#reserved-balance-value').text(response.reserved_balance || 0);

                        if (response.transactions && response.transactions.length > 0) {
                            let rows = '';

                            response.transactions.forEach(function(transaction) {
                                const isCharge = transaction.type === 1;
                                const badgeClass = isCharge ? 'success' : (transaction.type === 2 ?
                                    'danger' : 'warning');
                                const textClass = isCharge ? 'success' : (transaction.type === 2 ?
                                    'danger' : 'warning');
                                const sign = isCharge ? '+' : '-';

                                rows += '<tr>' +
                                    '<td><span class="badge badge-' + badgeClass + '">' +
                                    transaction.type_label + '</span></td>' +
                                    '<td><strong class="text-' + textClass + '">' + sign +
                                    transaction.amount +
                                    ' {{ __('dashboard.currency') }}</strong></td>' +
                                    '<td>' + transaction.balance_after +
                                    ' {{ __('dashboard.currency') }}</td>' +
                                    '<td>' + transaction.created_at + '</td>' +
                                    '<td><span class="badge badge-success">{{ __('dashboard.completed') }}</span></td>' +
                                    '</tr>';
                            });

                            $('#wallet-transactions-tbody').html(rows);
                            $('#wallet-transactions-count').text(response.transactions.length);
                            $('#transactions-table-wrapper').show();
                            $('#no-transactions-message').hide();
                        }

                        showActionSuccess(response.msg, function() {
                            $form[0].reset();
                            $('#chargeWalletModal').modal('hide');
                        });
                    },
                    error: function(xhr) {
                        showActionError(xhr.responseJSON?.msg);
                    },
                    complete: function() {
                        $submitButton.prop('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            });

            $(document).on('click', '.toggle-active-btn-show', function(e) {
                e.preventDefault();

                const url = $(this).data('url');

                showActionAlert({
                    text: '{{ __('dashboard.toggle_active_text') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performShowRequest({
                            url: url
                        });
                    }
                });
            });

            $(document).on('click', '.toggle-block-btn-show', function(e) {
                e.preventDefault();

                const $button = $(this);
                const url = $button.data('url');
                const isBlocked = $button.data('blocked') == 1;
                const blockText = isBlocked ? '{{ __('dashboard.un_block') }}' : '{{ __('dashboard.block') }}';

                showActionAlert({
                    text: '{{ __('dashboard.toggle_block_text') }}' + ' (' + blockText + ')'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performShowRequest({
                            url: url
                        });
                    }
                });
            });

            $(document).on('click', '.delete-row-show', function(e) {
                e.preventDefault();

                const url = $(this).data('url');

                showActionAlert({
                    text: '{{ __('dashboard.delete_confirmation') }}',
                    icon: 'warning',
                    confirmColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performShowRequest({
                            url: url,
                            method: 'DELETE',
                            onSuccess: function() {
                                window.location.href =
                                    '{{ route($indexRouteName ?? 'admin.users.index') }}';
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

    <x-dashboard.users.send-notification-modal :userId="$row->id" />
    <x-dashboard.users.charge-wallet-modal :userId="$row->id" :currentBalance="$row->wallet->available_balance ?? 0" />
</x-dashboard.layouts.master>
