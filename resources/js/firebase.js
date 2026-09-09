import { initializeApp } from 'firebase/app';
import { getFirestore } from 'firebase/firestore';

const firebase = initializeApp({
    apiKey: "AIzaSyA47Y34ugcw3dH9bhyEIXVwwtp1euwdc7M",
    authDomain: "shopcart-laravel01.firebaseapp.com",
    projectId: "shopcart-laravel01",
    storageBucket: "shopcart-laravel01.firebasestorage.app",
    messagingSenderId: "686630699835",
    appId: "1:686630699835:web:1fcdb50e0d59f17c2e2d1e",
    measurementId: "G-TQ4QBNTK2F"
});

export const db = getFirestore(firebase);
