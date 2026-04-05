
<div class="col-lg-6 col-md-12 col-sm-12 welcome-card-wrapper">
    <div class="card bg-analytics text-white">
        <div class="card-content">
            <div class="card-body text-center">
                <img src="{{ asset('dashboardAssets/app-assets/images/elements/decore-left.png') }}" class="img-left"
                    alt="card-img-left">
                <img src="{{ asset('dashboardAssets/app-assets/images/elements/decore-right.png') }}" class="img-right"
                    alt="card-img-right">
                <div class="avatar avatar-lg bg-primary shadow mt-0">
                    <div class="avatar-content">
                        <i class="feather icon-award white"></i>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="mb-1 text-white welcome-title">{{ __('dashboard.welcome') . ' ' . auth()->user()->name }} </h3>
                    <p class="m-auto w-75 welcome-subtitle">{{ __('dashboard.welcome sentence') }}</p>
                    <!-- Animated Character -->
                    <div class="character-container">
                        <div class="cute-character">
                            <div class="character-head">
                                <div class="character-face">
                                    <div class="character-eyes">
                                        <div class="character-eye">
                                            <div class="character-pupil"></div>
                                        </div>
                                        <div class="character-eye">
                                            <div class="character-pupil"></div>
                                        </div>
                                    </div>
                                    <div class="character-smile"></div>
                                    <div class="character-cheeks">
                                        <div class="cheek"></div>
                                        <div class="cheek"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="character-body">
                                <div class="character-arms">
                                    <div class="character-arm left-arm"></div>
                                    <div class="character-arm right-arm"></div>
                                </div>
                            </div>
                            <div class="character-feet">
                                <div class="character-foot"></div>
                                <div class="character-foot"></div>
                            </div>
                            <div class="sparkles">
                                <div class="sparkle sparkle-1">✨</div>
                                <div class="sparkle sparkle-2">⭐</div>
                                <div class="sparkle sparkle-3">💫</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-6 col-md-12 col-sm-12 welcome-card-wrapper">
    <div class="card bg-analytics info-dashboard-card">
        <div class="card-content">
            <div class="card-body">
                <!-- Date & Time Section -->
                <div class="info-section date-time-section">
                    <div class="info-icon">
                        <i class="feather icon-calendar text-primary"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">{{ __('dashboard.date') }}</div>
                        <div class="info-value" id="current-date"></div>
                        <div class="info-time" id="current-time"></div>
                    </div>
                </div>

                <!-- Weather Section -->
                <div class="info-section weather-section">
                    <div class="info-icon">
                        <i class="feather icon-cloud text-info"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">{{ __('dashboard.weather') }}</div>
                        <div class="weather-info" id="weather-info">
                            <div class="weather-loading">
                                <i class="feather icon-loader"></i>
                                <span>{{ __('dashboard.loading') }}...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .welcome-card-wrapper {
        display: flex;
    }

    .welcome-card-wrapper .card {
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .info-dashboard-card {
        height: 100%;
        background: linear-gradient(135deg, #8B7EC8 0%, #A594F9 100%);
        color: white;
        display: flex;
        flex-direction: column;
    }

    .info-dashboard-card .card-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .info-dashboard-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .welcome-card-wrapper .card-body {
        padding: 1rem 1.5rem !important;
    }

    .bg-analytics .card-body {
        padding: 1.25rem 1.5rem !important;
    }

    .bg-analytics .avatar {
        width: 3rem !important;
        height: 3rem !important;
    }

    .bg-analytics .avatar i {
        font-size: 1.25rem !important;
    }

    .welcome-title {
        font-size: 1.5rem !important;
        font-weight: 600;
    }

    .welcome-subtitle {
        font-size: 0.9rem !important;
        margin-bottom: 0.5rem !important;
    }

    .info-section {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .info-section:hover {
        background: rgba(255, 255, 255, 0.18);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .info-icon {
        font-size: 1.5rem;
        margin-left: 0.75rem;
        opacity: 0.9;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .info-time {
        font-size: 0.875rem;
        opacity: 0.9;
        font-weight: 500;
    }

    .weather-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .weather-loading {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        animation: pulse 2s infinite;
    }

    .weather-loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .weather-temp {
        font-size: 1.2rem;
        font-weight: 600;
    }

    .weather-desc {
        font-size: 0.8rem;
        opacity: 0.9;
        text-transform: capitalize;
    }

    .weather-icon {
        font-size: 1.5rem;
    }

    /* Animated Cute Character Styles */
    .character-container {
        position: relative;
        height: 90px;
        width: 100%;
        margin-top: 0.75rem;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cute-character {
        position: relative;
        width: 50px;
        height: 65px;
        animation: characterFloat 3s ease-in-out infinite;
    }

    .character-head {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #FFE5B4 0%, #FFDAB9 100%);
        border-radius: 50%;
        position: relative;
        margin: 0 auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 3;
    }

    .character-face {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .character-eyes {
        display: flex;
        gap: 8px;
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
    }

    .character-eye {
        width: 7px;
        height: 7px;
        background: white;
        border-radius: 50%;
        position: relative;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .character-pupil {
        width: 4px;
        height: 4px;
        background: #333;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: characterBlink 4s infinite;
    }

    .character-smile {
        width: 16px;
        height: 8px;
        border: 2px solid #333;
        border-top: none;
        border-radius: 0 0 16px 16px;
        position: absolute;
        bottom: 9px;
        left: 50%;
        transform: translateX(-50%);
    }

    .character-cheeks {
        position: absolute;
        top: 18px;
        width: 100%;
    }

    .cheek {
        width: 6px;
        height: 6px;
        background: rgba(255, 182, 193, 0.6);
        border-radius: 50%;
        position: absolute;
        animation: cheekPulse 2s ease-in-out infinite;
    }

    .cheek:first-child {
        left: 8px;
    }

    .cheek:last-child {
        right: 8px;
    }

    .character-body {
        width: 32px;
        height: 22px;
        background: linear-gradient(135deg, #8B7EC8 0%, #A594F9 100%);
        border-radius: 16px 16px 10px 10px;
        position: relative;
        margin: -3px auto 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        z-index: 2;
    }

    .character-arms {
        position: absolute;
        top: 8px;
        width: 100%;
        height: 20px;
    }

    .character-arm {
        width: 4px;
        height: 13px;
        background: linear-gradient(135deg, #8B7EC8 0%, #A594F9 100%);
        border-radius: 2px;
        position: absolute;
        top: 0;
        transform-origin: top center;
    }

    .character-arm.left-arm {
        left: -8px;
        animation: armWave 2s ease-in-out infinite;
    }

    .character-arm.right-arm {
        right: -8px;
        animation: armWave 2s ease-in-out infinite 0.3s;
    }

    .character-feet {
        position: absolute;
        top: 60px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 5px;
        z-index: 1;
    }

    .character-foot {
        width: 8px;
        height: 5px;
        background: #333;
        border-radius: 0 0 6px 6px;
        animation: footBounce 0.6s ease-in-out infinite;
    }

    .character-foot:nth-child(2) {
        animation-delay: 0.3s;
    }

    .sparkles {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
    }

    .sparkle {
        position: absolute;
        font-size: 16px;
        opacity: 0;
        animation: sparkleFloat 3s ease-in-out infinite;
    }

    .sparkle-1 {
        top: 10px;
        left: 20px;
        animation-delay: 0s;
    }

    .sparkle-2 {
        top: 30px;
        right: 15px;
        animation-delay: 1s;
    }

    .sparkle-3 {
        bottom: 20px;
        left: 50%;
        animation-delay: 2s;
    }

    @keyframes characterFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes characterBlink {
        0%, 90%, 100% {
            transform: translate(-50%, -50%) scaleY(1);
        }
        95% {
            transform: translate(-50%, -50%) scaleY(0.1);
        }
    }

    @keyframes cheekPulse {
        0%, 100% {
            opacity: 0.6;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.1);
        }
    }

    @keyframes armWave {
        0%, 100% {
            transform: rotate(-10deg);
        }
        50% {
            transform: rotate(10deg);
        }
    }

    @keyframes footBounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }

    @keyframes sparkleFloat {
        0%, 100% {
            opacity: 0;
            transform: translateY(0) scale(0.5);
        }
        50% {
            opacity: 1;
            transform: translateY(-15px) scale(1);
        }
    }

    @media (max-width: 768px) {
        .character-container {
            height: 70px;
        }

        .cute-character {
            width: 40px;
            height: 55px;
        }

        .character-head {
            width: 32px;
            height: 32px;
        }

        .character-body {
            width: 26px;
            height: 18px;
        }

        .welcome-title {
            font-size: 1.25rem !important;
        }

        .welcome-subtitle {
            font-size: 0.8rem !important;
        }
    }

    @media (max-width: 768px) {
        .info-section {
            padding: 0.6rem;
            margin-bottom: 0.5rem;
        }

        .info-icon {
            font-size: 1.25rem;
            margin-left: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
        }

        .info-time {
            font-size: 0.75rem;
        }
    }

    /* Dark Mode Improvements */
    body.dark-layout .welcome-title {
        color: #ebeefd !important;
    }

    body.dark-layout .welcome-subtitle {
        color: #c2c6dc !important;
    }

    body.dark-layout .info-value {
        color: #ebeefd !important;
    }

    body.dark-layout .info-time {
        color: #b4b7bd !important;
    }

    body.dark-layout .info-label {
        color: #c2c6dc !important;
    }

    body.dark-layout .weather-temp,
    body.dark-layout .weather-desc {
        color: #ebeefd !important;
    }
</style>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Update Date & Time
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: {{ app()->getLocale() == 'ar' ? 'true' : 'false' }}
            };

            const dateStr = now.toLocaleDateString('{{ app()->getLocale() == "ar" ? "ar-SA" : "en-US" }}', options);
            const timeStr = now.toLocaleTimeString('{{ app()->getLocale() == "ar" ? "ar-SA" : "en-US" }}', timeOptions);

            $('#current-date').text(dateStr);
            $('#current-time').text(timeStr);
        }

        // Update every second
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Weather API (using OpenWeatherMap - free tier)
        function loadWeather() {
            // Default location (Riyadh, Saudi Arabia) - يمكن تغييرها حسب الحاجة
            const city = 'Riyadh';
            const apiKey = '{{ env("WEATHER_API_KEY", "") }}';

            if (!apiKey) {
                // Fallback: Show static weather info if no API key
                $('#weather-info').html(`
                    <div class="weather-temp">25°C</div>
                    <div class="weather-desc">{{ __('dashboard.sunny') }}</div>
                    <div class="weather-icon">☀️</div>
                `);
                return;
            }

            // Using a free weather API service
            $.ajax({
                url: `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang={{ app()->getLocale() }}`,
                method: 'GET',
                success: function(data) {
                    const temp = Math.round(data.main.temp);
                    const desc = data.weather[0].description;
                    const icon = data.weather[0].icon;

                    let emoji = '☀️';
                    if (icon.includes('cloud')) emoji = '☁️';
                    else if (icon.includes('rain')) emoji = '🌧️';
                    else if (icon.includes('snow')) emoji = '❄️';
                    else if (icon.includes('thunder')) emoji = '⛈️';

                    $('#weather-info').html(`
                        <div class="weather-temp">${temp}°C</div>
                        <div class="weather-desc">${desc}</div>
                        <div class="weather-icon">${emoji}</div>
                    `);
                },
                error: function() {
                    $('#weather-info').html(`
                        <div class="weather-temp">--</div>
                        <div class="weather-desc">{{ __('dashboard.unavailable') }}</div>
                    `);
                }
            });
        }

        // Load weather on page load
        loadWeather();
        // Refresh weather every 30 minutes
        setInterval(loadWeather, 1800000);
    });
</script>
@endsection
