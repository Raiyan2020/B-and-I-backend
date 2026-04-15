<div class="col-lg-4 col-md-12 col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="feather icon-activity text-info"></i>
                {{ __('dashboard.recent activity') }}
            </h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="activity-list">
                    @forelse($activitySections as $section)
                        <div class="activity-section">
                            <h6 class="activity-section-title">
                                <i class="{{ $section['icon'] }} text-{{ $section['color'] }}"></i>
                                {{ $section['title'] }}
                            </h6>
                            @foreach($section['items'] as $item)
                                <div class="activity-item">
                                    <div class="activity-icon bg-{{ $section['color'] }}">
                                        <i class="{{ $section['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text">
                                            <strong>{{ $item['title'] }}</strong>
                                            {{ $item['description'] }}
                                        </p>
                                        <span class="activity-time">{{ $item['time'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="feather icon-inbox text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2 mb-0">{{ __('dashboard.no_recent_activity') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .activity-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .activity-section {
        margin-bottom: 1.5rem;
    }

    .activity-section:last-child {
        margin-bottom: 0;
    }

    .activity-section-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #5e5873;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e7e7e7;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f3f3;
        transition: all 0.3s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background-color: #f8f8f8;
        margin: 0 -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
        border-radius: 8px;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 0.875rem;
    }

    .activity-icon.bg-success {
        background: linear-gradient(135deg, #81C784 0%, #66BB6A 100%) !important;
    }

    .activity-icon.bg-danger {
        background: linear-gradient(135deg, #F5A5A5 0%, #E57373 100%) !important;
    }

    .activity-icon.bg-primary {
        background: linear-gradient(135deg, #9C88FF 0%, #8B7EC8 100%) !important;
    }

    .activity-icon.bg-info {
        background: linear-gradient(135deg, #64B5F6 0%, #42A5F5 100%) !important;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-text {
        margin: 0;
        font-size: 0.875rem;
        color: #5e5873;
        line-height: 1.5;
    }

    .activity-time {
        font-size: 0.75rem;
        color: #b4b7bd;
        margin-top: 0.25rem;
        display: block;
    }

    .activity-list::-webkit-scrollbar {
        width: 6px;
    }

    .activity-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .activity-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .activity-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    body.dark-layout .activity-section-title {
        color: #ebeefd !important;
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .activity-text {
        color: #c2c6dc !important;
    }

    body.dark-layout .activity-text strong {
        color: #ebeefd !important;
    }

    body.dark-layout .activity-time {
        color: #8b8fa8 !important;
    }

    body.dark-layout .activity-item {
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .activity-item:hover {
        background-color: #323a5a !important;
    }
</style>
@endpush
