<!DOCTYPE html>
<html class="loading" lang="{{app()->getLocale()}}" @if(app()->getLocale()=='ar') data-textdirection="rtl" @else data-textdirection="ltr" @endif>
<!-- BEGIN: Head-->

<x-dashboard.layouts.head  title="{{$title}}"/>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static   menu-expanded" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" id="body-tag">

<!-- BEGIN: Header-->
<x-dashboard.layouts.navbar />

<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a class="pb-25" href="#">
            <h6 class="text-primary mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="{{asset('dashboardAssets/app-assets/images/icons/xls.png')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="{{asset('dashboardAssets/app-assets/images/icons/jpg.png')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="{{asset('dashboardAssets/app-assets/images/icons/pdf.png')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="{{asset('dashboardAssets/app-assets/images/icons/doc.png')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
        </a></li>
    <li class="d-flex align-items-center"><a class="pb-25" href="#">
            <h6 class="text-primary mb-0">Members</h6>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="{{asset('dashboardAssets/app-assets/images/portrait/small/avatar-s-8.jpg')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="{{asset('dashboardAssets/app-assets/images/portrait/small/avatar-s-1.jpg')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="{{asset('dashboardAssets/app-assets/images/portrait/small/avatar-s-14.jpg')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="{{asset('dashboardAssets/app-assets/images/portrait/small/avatar-s-6.jpg')}}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No results found.</span></div>
        </a></li>
</ul>
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
<x-dashboard.layouts.sidebar />
<!-- END: Main Menu-->

{{$slot}}



<div class="sidenav-overlay"></div>
<div class="drag-target"></div>


<x-dashboard.layouts.footer />
@php
    $firebaseConfig = [
        'apiKey' => config('services.firebase.api_key'),
        'authDomain' => config('services.firebase.auth_domain'),
        'projectId' => config('services.firebase.project_id'),
        'storageBucket' => config('services.firebase.storage_bucket'),
        'messagingSenderId' => config('services.firebase.messaging_sender_id'),
        'appId' => config('services.firebase.app_id'),
        'vapidKey' => config('services.firebase.vapid_key'),
        'tokenEndpoint' => route('admin.fcmToken'),
        'deleteTokenEndpoint' => route('admin.fcmToken.destroy'),
        'logoutUrl' => route('admin.logout'),
        'locale' => app()->getLocale(),
        'csrfToken' => csrf_token(),
        'notificationSoundUrl' => asset('dashboardAssets/sounds/admin-notification.wav'),
    ];
@endphp
@if (filled(config('services.firebase.api_key')) && filled(config('services.firebase.messaging_sender_id')) && filled(config('services.firebase.app_id')))
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.12.0/firebase-app.js';
        import { getMessaging, getToken, isSupported, onMessage } from 'https://www.gstatic.com/firebasejs/12.12.0/firebase-messaging.js';

        const firebaseConfig = @json($firebaseConfig);
        const adminTokenStorageKey = 'admin_firebase_device_token';
        const notificationCounter = document.getElementById('notification-counter');
        const notificationHeaderCount = document.getElementById('notification-header-count');
        const notificationList = document.getElementById('notification-list');
        let adminNotificationAudio = null;

        function playAdminNotificationSound() {
            if (!firebaseConfig.notificationSoundUrl) {
                return;
            }

            try {
                if (!adminNotificationAudio) {
                    adminNotificationAudio = new Audio(firebaseConfig.notificationSoundUrl);
                    adminNotificationAudio.preload = 'auto';
                }

                adminNotificationAudio.currentTime = 0;
                const playPromise = adminNotificationAudio.play();

                if (playPromise) {
                    playPromise.catch(() => {});
                }
            } catch (error) {
                console.warn('Admin notification sound failed:', error);
            }
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event.data?.type === 'ADMIN_NOTIFICATION_SOUND') {
                    playAdminNotificationSound();
                }
            });
        }

        function unlockAdminNotificationAudio() {
            if (!firebaseConfig.notificationSoundUrl) {
                return;
            }

            if (!adminNotificationAudio) {
                adminNotificationAudio = new Audio(firebaseConfig.notificationSoundUrl);
                adminNotificationAudio.preload = 'auto';
            }

            adminNotificationAudio.volume = 0.01;
            adminNotificationAudio.play()
                .then(() => {
                    adminNotificationAudio.pause();
                    adminNotificationAudio.currentTime = 0;
                    adminNotificationAudio.volume = 1;
                })
                .catch(() => {
                    adminNotificationAudio.volume = 1;
                });
        }

        document.addEventListener('click', unlockAdminNotificationAudio, { once: true });
        document.addEventListener('keydown', unlockAdminNotificationAudio, { once: true });

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function incrementNotificationCounter() {
            if (!notificationCounter || !notificationHeaderCount) {
                return;
            }

            const currentCount = Number.parseInt(notificationCounter.textContent || '0', 10) || 0;
            const nextCount = currentCount + 1;

            notificationCounter.textContent = String(nextCount);

            const newLabel = notificationHeaderCount.textContent?.trim().replace(/^\d+/, String(nextCount));
            notificationHeaderCount.textContent = newLabel || String(nextCount);
        }

        function prependNotificationItem({ notificationId, title, body, clickAction }) {
            if (!notificationList || !notificationId) {
                return;
            }

            if (document.getElementById(`notification-${notificationId}`)) {
                return;
            }

            const item = document.createElement('a');
            item.className = 'd-flex justify-content-between notification-link';
            item.id = `notification-${notificationId}`;
            item.href = clickAction || `/admin/notifications/${notificationId}/read`;
            item.innerHTML = `
                <div class="media d-flex align-items-start">
                    <div class="media-left"><i class="feather icon-x-circle font-medium-5 primary"></i></div>
                    <div class="media-body">
                        <h6 class="primary media-heading">${escapeHtml(title)}!</h6>
                        <small class="notification-text">${escapeHtml(body)}</small>
                    </div>
                    <small><time class="media-meta">${escapeHtml(new Date().toLocaleString())}</time></small>
                </div>
            `;

            notificationList.prepend(item);
        }

        async function forceAdminLogout() {
            try {
                await deleteAdminFirebaseToken();
            } catch (error) {
                console.warn('Forced admin logout cleanup failed:', error);
                localStorage.removeItem(adminTokenStorageKey);
            }

            window.location.href = `${firebaseConfig.logoutUrl}?device_token=${encodeURIComponent(localStorage.getItem(adminTokenStorageKey) || '')}`;
        }

        async function registerAdminFirebaseToken() {
            if (!('serviceWorker' in navigator) || !('Notification' in window)) {
                return;
            }

            const supported = await isSupported().catch(() => false);
            if (!supported) {
                return;
            }

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

            if (Notification.permission === 'default') {
                await Notification.requestPermission();
            }

            if (Notification.permission !== 'granted') {
                return;
            }

            const tokenOptions = {
                serviceWorkerRegistration: registration,
            };

            if (firebaseConfig.vapidKey) {
                tokenOptions.vapidKey = firebaseConfig.vapidKey;
            }

            const token = await getToken(messaging, tokenOptions);
            if (!token) {
                return;
            }

            const response = await fetch(firebaseConfig.tokenEndpoint, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': firebaseConfig.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    token,
                    device_type: 'web',
                    locale: firebaseConfig.locale,
                }),
            });

            if (!response.ok) {
                throw new Error(`Failed to sync admin Firebase token: ${response.status}`);
            }

            localStorage.setItem(adminTokenStorageKey, token);

            onMessage(messaging, ({ notification, data }) => {
                if (Notification.permission !== 'granted') {
                    return;
                }

                const title = notification?.title || data?.title || 'New notification';
                const body = notification?.body || data?.body || '';
                const notificationId = data?.notification_id || '';
                const clickAction = notificationId
                    ? `/admin/notifications/${notificationId}/read`
                    : (data?.click_action || notification?.click_action || '/');

                incrementNotificationCounter();
                prependNotificationItem({
                    notificationId,
                    title,
                    body,
                    clickAction,
                });

                playAdminNotificationSound();

                registration.showNotification(title, {
                    body,
                    icon: notification?.icon || data?.icon || '/favicon.ico',
                    silent: false,
                    data: {
                        click_action: clickAction,
                    },
                });

                if (data?.notification_type === 'admin_blocked' && ['1', 'true'].includes(String(data?.force_logout))) {
                    forceAdminLogout();
                }
            });
        }

        async function deleteAdminFirebaseToken() {
            const token = localStorage.getItem(adminTokenStorageKey);
            if (!token) {
                return;
            }

            const response = await fetch(firebaseConfig.deleteTokenEndpoint, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': firebaseConfig.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ token }),
                keepalive: true,
            });

            if (response.ok) {
                localStorage.removeItem(adminTokenStorageKey);
            }
        }

        document.querySelectorAll('.js-admin-logout').forEach((link) => {
            link.addEventListener('click', async (event) => {
                event.preventDefault();

                try {
                    await deleteAdminFirebaseToken();
                } catch (error) {
                    console.warn('Firebase admin logout cleanup failed:', error);
                }

                window.location.href = `${firebaseConfig.logoutUrl}?device_token=${encodeURIComponent(localStorage.getItem(adminTokenStorageKey) || '')}`;
            });
        });

        registerAdminFirebaseToken().catch((error) => {
            console.warn('Firebase admin dashboard setup failed:', error);
        });
    </script>
@endif
</body>
<!-- END: Body-->

</html>
