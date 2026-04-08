<x-dashboard.layouts.master title="{{ __('dashboard.opportunity_details') }}">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <x-dashboard.layouts.breadcrumb now="{{ __('dashboard.opportunity_details') }}">
                <li class="breadcrumb-item"><a href="{{ route('admin.opportunities.index') }}">{{ __('dashboard.opportunities_list') }}</a></li>
            </x-dashboard.layouts.breadcrumb>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">{{ __('dashboard.opportunity_details') }}</h4></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.company_name') }}:</strong> {{ $row->company_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.goal') }}:</strong> {{ __('dashboard.goal_'.$row->goal->value) }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.table status') }}:</strong> {{ __('dashboard.opportunity_status_'.$row->status->value) }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.category') }}:</strong> {{ $row->category?->getTranslation('name', app()->getLocale()) ?? '' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_name') }}:</strong> {{ $row->contact_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_phone') }}:</strong> {{ $row->contact_phone }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.contact_email') }}:</strong> {{ $row->contact_email }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.owner_name') }}:</strong> {{ $row->owner_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.admin_company_name') }}:</strong> {{ $row->admin_company_name }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.license_number') }}:</strong> {{ $row->license_number }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.business_age_years') }}:</strong> {{ $row->business_age_years }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.investment_required') }}:</strong> {{ $row->investment_required }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.business_stage') }}:</strong> {{ $row->business_stage }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.sale_percentage') }}:</strong> {{ $row->sale_percentage ?? '-' }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.legal_entity') }}:</strong> {{ $row->legal_entity }}</div>
                                <div class="col-md-6 mb-2"><strong>{{ __('dashboard.financial_status') }}:</strong> {{ $row->financial_status }}</div>
                                <div class="col-12 mb-2"><strong>{{ __('dashboard.investment_reason') }}:</strong><br>{{ $row->investment_reason }}</div>
                                <div class="col-12 mb-2"><strong>{{ __('dashboard.full_description') }}:</strong><br>{{ $row->full_description }}</div>
                                @if($row->review_note)
                                    <div class="col-12 mb-2"><strong>{{ __('dashboard.review_note') }}:</strong><br>{{ $row->review_note }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">{{ __('dashboard.review_opportunity') }}</h4></div>
                        <div class="card-body">
                            <form class="form form-vertical store" method="POST" action="{{ route('admin.opportunities.review', $row) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.table status') }}</label>
                                            <select class="form-control" name="status" required>
                                                @foreach($reviewStatuses as $value => $label)
                                                    <option value="{{ $value }}" {{ $row->status->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('dashboard.review_note') }}</label>
                                            <textarea class="form-control" name="review_note" rows="4">{{ $row->review_note }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary submit_button">{{ __('dashboard.save_review') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layouts.master>
