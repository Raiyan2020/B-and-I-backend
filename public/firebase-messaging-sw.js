// // Firebase Admin Dashboard Setup
importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyCKgdoyzkATKnK1hY_pfCciSzMdkj0GCQ0",
    authDomain: "bandi-c8de1.firebaseapp.com",
    projectId: "bandi-c8de1",
    storageBucket: "bandi-c8de1.firebasestorage.app",
    messagingSenderId: "1081174689553",
    appId: "1:1081174689553:web:8596f2da5ca687a5c1aeb0",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const notification = payload.notification || {};
    const data = payload.data || {};
    const title = notification.title || data.title || 'New notification';

    return self.registration.showNotification(title, {
        body: notification.body || data.body || '',
        icon: notification.icon || data.icon || '/favicon.ico',
        data: {
            click_action: data.click_action || notification.click_action || '/',
        },
    });
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const targetUrl = event.notification?.data?.click_action || "/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then(function (clientList) {
                for (const client of clientList) {
                    if (client.url === targetUrl && "focus" in client) {
                        return client.focus();
                    }
                }

                if (clients.openWindow) {
                    return clients.openWindow(targetUrl);
                }
            }),
    );
    // End Firebase Admin Dashboard Setup
});
