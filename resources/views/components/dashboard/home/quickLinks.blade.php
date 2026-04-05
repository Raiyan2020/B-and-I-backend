<div class="col-12">
    <div class="card quick-links-card">
        <div class="card-header">
            <h4 class="card-title mb-0">
                <i class="feather icon-zap text-warning"></i>
                {{ __('dashboard.quick links') }}
            </h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="quick-links-grid">
                    <a href="{{ route('admin.admins.create') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-danger">
                            <i class="feather icon-user-plus"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.add admin') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.add new admin account') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-success">
                            <i class="feather icon-user-plus"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.add user') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.add new client account') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.categories.create') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-primary">
                            <i class="feather icon-plus-circle"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.add category') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.add new category') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.generalSetting.index') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-info">
                            <i class="feather icon-settings"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.general settings') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.manage general settings') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.profile') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-warning">
                            <i class="feather icon-user"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.Edit Profile') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.edit your profile') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.roles.index') }}" class="quick-link-item">
                        <div class="quick-link-icon bg-secondary">
                            <i class="feather icon-lock"></i>
                        </div>
                        <div class="quick-link-content">
                            <h6 class="quick-link-title">{{ __('dashboard.roles list') }}</h6>
                            <p class="quick-link-desc">{{ __('dashboard.manage roles and permissions') }}</p>
                        </div>
                        <div class="quick-link-arrow">
                            <i class="feather icon-arrow-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .quick-links-card {
        margin-top: 1.5rem;
    }

    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .quick-link-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: #fff;
        border: 1.5px solid #f0f0f0;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .quick-link-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #8B7EC8 0%, #A594F9 100%);
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.3s ease;
    }

    .quick-link-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #B8A9E0;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    }

    .quick-link-item:hover::before {
        transform: scaleY(1);
    }

    .quick-link-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
        transition: all 0.3s ease;
    }

    .quick-link-item:hover .quick-link-icon {
        transform: scale(1.08) rotate(3deg);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.15);
    }

    .quick-link-content {
        flex: 1;
        min-width: 0;
    }

    .quick-link-title {
        margin: 0 0 0.25rem 0;
        font-size: 1rem;
        font-weight: 600;
        color: #5e5873;
        transition: color 0.3s ease;
    }

    .quick-link-item:hover .quick-link-title {
        color: #8B7EC8;
    }

    .quick-link-desc {
        margin: 0;
        font-size: 0.875rem;
        color: #b4b7bd;
        line-height: 1.4;
    }

    .quick-link-arrow {
        color: #b4b7bd;
        font-size: 1.25rem;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .quick-link-item:hover .quick-link-arrow {
        color: #8B7EC8;
        transform: translateX({{ app()->getLocale() == 'ar' ? '-5px' : '5px' }});
    }

    /* Icon Background Colors - Softer and more pleasant */
    .quick-link-icon.bg-danger {
        background: linear-gradient(135deg, #F5A5A5 0%, #E57373 100%);
    }

    .quick-link-icon.bg-success {
        background: linear-gradient(135deg, #81C784 0%, #66BB6A 100%);
    }

    .quick-link-icon.bg-primary {
        background: linear-gradient(135deg, #9C88FF 0%, #8B7EC8 100%);
    }

    .quick-link-icon.bg-info {
        background: linear-gradient(135deg, #64B5F6 0%, #42A5F5 100%);
    }

    .quick-link-icon.bg-warning {
        background: linear-gradient(135deg, #FFB74D 0%, #FFA726 100%);
    }

    .quick-link-icon.bg-secondary {
        background: linear-gradient(135deg, #A0A0A0 0%, #8E8E8E 100%);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .quick-links-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .quick-link-item {
            padding: 1rem;
        }

        .quick-link-icon {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
        }

        .quick-link-title {
            font-size: 0.95rem;
        }

        .quick-link-desc {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .quick-links-grid {
            gap: 0.5rem;
        }

        .quick-link-item {
            padding: 0.875rem;
            gap: 0.75rem;
        }
    }

    /* Animation */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .quick-link-item {
        animation: slideInUp 0.5s ease-out;
    }

    .quick-link-item:nth-child(1) { animation-delay: 0.1s; }
    .quick-link-item:nth-child(2) { animation-delay: 0.2s; }
    .quick-link-item:nth-child(3) { animation-delay: 0.3s; }
    .quick-link-item:nth-child(4) { animation-delay: 0.4s; }
    .quick-link-item:nth-child(5) { animation-delay: 0.5s; }
    .quick-link-item:nth-child(6) { animation-delay: 0.6s; }

    /* Dark Mode Improvements */
    body.dark-layout .quick-link-item {
        background: #2b3553 !important;
        border-color: #414561 !important;
    }

    body.dark-layout .quick-link-title {
        color: #ebeefd !important;
        font-weight: 600;
    }

    body.dark-layout .quick-link-desc {
        color: #b4b7bd !important;
    }

    body.dark-layout .quick-link-arrow {
        color: #b4b7bd !important;
    }

    body.dark-layout .quick-link-item:hover {
        background: #323a5a !important;
        border-color: #8B7EC8 !important;
    }

    body.dark-layout .quick-link-item:hover .quick-link-title {
        color: #ffffff !important;
    }

    body.dark-layout .quick-link-item:hover .quick-link-desc {
        color: #d0d4e0 !important;
    }

    body.dark-layout .quick-links-card .card-header {
        background: #2b3553 !important;
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .quick-links-card .card-header h4 {
        color: #ebeefd !important;
    }
</style>
@endpush
