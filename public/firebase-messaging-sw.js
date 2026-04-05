importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyAKYL_WxuH0krn4e1wbwwJTUUEWoIZbQIY",
    projectId: "restaurants-f7a7e",
    messagingSenderId: "747019248975",
    appId: "1:747019248975:web:9869b9fbac45d09fecd13a",
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});
