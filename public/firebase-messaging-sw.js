importScripts('https://www.gstatic.com/firebasejs/11.6.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging-compat.js');

firebase.initializeApp({

    apiKey: "AIzaSyA47Y34ugcw3dH9bhyEIXVwwtp1euwdc7M",
    authDomain: "shopcart-laravel01.firebaseapp.com",
    projectId: "shopcart-laravel01",
    storageBucket: "shopcart-laravel01.firebasestorage.app",
    messagingSenderId: "686630699835",
    appId: "1:686630699835:web:1fcdb50e0d59f17c2e2d1e",

});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {

    console.log(
        '[firebase-messaging-sw.js] Background message ',
        payload
    );

    const notificationTitle =
        payload.notification.title;

    const notificationOptions = {
        body: payload.notification.body,
        icon: '/favicon.ico'
    };

    self.registration.showNotification(
        notificationTitle,
        notificationOptions
    );
});