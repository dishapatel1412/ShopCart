<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopCart | Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    @include('admin.layout.header')

    <div class="container-fluid flex-grow-1">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-md-2 bg-light min-vh-100 pt-4 px-0 border-end">
                @include('admin.layout.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="col-md-10 pt-4 px-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @yield('content')
            </div>

        </div>
    </div>

    @include('admin.layout.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @auth
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import {
            getMessaging,
            getToken,
            onMessage
        } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js";
        
        const firebaseConfig = {
            apiKey: "AIzaSyA47Y34ugcw3dH9bhyEIXVwwtp1euwdc7M",
            authDomain: "shopcart-laravel01.firebaseapp.com",
            projectId: "shopcart-laravel01",
            storageBucket: "shopcart-laravel01.firebasestorage.app",
            messagingSenderId: "686630699835",
            appId: "1:686630699835:web:1fcdb50e0d59f17c2e2d1e"
        };
        
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        
        async function initializeFirebaseNotifications() {
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    return;
                }
                
                const token = await getToken(messaging, {
                    vapidKey: "BEY-9NrqrneHSgmei8BhFoifzQo5abJDvcovdO2c-t3nYhv01dCaa_kbznMSEIGJK1ZgOp86xfY8jdT5GdW1mKI"
                });
                
                if (!token) {
                    return;
                }
                
                await fetch('/save-device-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                    },
                    body: JSON.stringify({
                        device_token: token
                    })
                });
            } catch (error) {
                console.error(error);
            }
        }
        
        initializeFirebaseNotifications();
        
        onMessage(messaging, (payload) => {
            console.log(payload);

            // if (payload.notification) {
            //     new Notification(payload.notification.title, {
            //         body: payload.notification.body,
            //         icon: '/favicon.ico'
            //     });
            // }
        });
    </script>
        @endauth
        @yield('scripts')
    </body>
</html>