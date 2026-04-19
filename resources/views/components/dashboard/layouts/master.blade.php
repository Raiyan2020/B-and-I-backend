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
// Firebase Admin Dashboard Setup //
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
        'locale' => app()->getLocale(),
        'csrfToken' => csrf_token(),
    ];
@endphp
@if (filled(config('services.firebase.api_key')) && filled(config('services.firebase.messaging_sender_id')) && filled(config('services.firebase.app_id')))
    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.12.0/firebase-app.js';
        import { getMessaging, getToken, isSupported, onMessage } from 'https://www.gstatic.com/firebasejs/12.12.0/firebase-messaging.js';

        const firebaseConfig = @json($firebaseConfig);

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

            await fetch(firebaseConfig.tokenEndpoint, {
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

            onMessage(messaging, ({ notification, data }) => {
                if (Notification.permission !== 'granted') {
                    return;
                }

                const title = notification?.title || data?.title || 'New notification';
                new Notification(title, {
                    body: notification?.body || data?.body || '',
                    icon: notification?.icon || data?.icon || '/favicon.ico',
                });
            });
        }

        registerAdminFirebaseToken().catch((error) => {
            console.warn('Firebase admin dashboard setup failed:', error);
        });
    </script>
@endif
// End Firebase Admin Dashboard Setup //
</body>
<!-- END: Body-->

</html>
