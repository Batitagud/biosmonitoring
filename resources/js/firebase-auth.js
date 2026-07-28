// resources/js/firebase-auth.js
// npm install firebase   (SDK versi 10+, modular)

import { initializeApp } from "firebase/app";
import {
    getAuth,
    createUserWithEmailAndPassword,
    signInWithEmailAndPassword,
    signInWithPopup,
    GoogleAuthProvider,
    updateProfile,
    signOut,
} from "firebase/auth";

// Config ini aman ditaruh di browser (bukan rahasia) — sesuai .env FIREBASE_*_WEB
const firebaseConfig = {
    apiKey: "AIzaSyCtu01esoogLkQlPsQ5Ls6zNxawMebdEM8",
    authDomain: "biosensormonitoring.firebaseapp.com",
    databaseURL: "https://biosensormonitoring-default-rtdb.firebaseio.com",
    projectId: "biosensormonitoring",
    storageBucket: "biosensormonitoring.firebasestorage.app",
    messagingSenderId: "268296678678",
    appId: "1:268296678678:web:f545c0a2bb34985bde3437",
    measurementId: "G-CXRDKXNZ0E",
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

// Kirim ID token ke Laravel supaya dibuatkan session server-side
async function establishServerSession(user) {
    const idToken = await user.getIdToken();
    const res = await fetch("/auth/session", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ idToken }),
    });
    if (!res.ok) {
        const text = await res.text();
        console.error("[/auth/session gagal]", res.status, text);
        throw new Error(
            "Gagal membuat sesi server (status " + res.status + ")",
        );
    }
    const data = await res.json();
    window.location.href = data.redirect || "/dashboard";
}

// ── REGISTER ──────────────────────────────────────────────────────────────
window.handleRegister = async function (name, email, password) {
    try {
        const cred = await createUserWithEmailAndPassword(
            auth,
            email,
            password,
        );
        await updateProfile(cred.user, { displayName: name });
        await establishServerSession(cred.user);
    } catch (e) {
        console.error("[Firebase Error]", e.code, e.message);
        showAuthError(mapFirebaseError(e.code));
    }
};

// ── LOGIN EMAIL/PASSWORD ──────────────────────────────────────────────────
window.handleLogin = async function (email, password) {
    try {
        const cred = await signInWithEmailAndPassword(auth, email, password);
        await establishServerSession(cred.user);
    } catch (e) {
        console.error("[Firebase Error]", e.code, e.message);
        showAuthError(mapFirebaseError(e.code));
    }
};

// ── LOGIN GOOGLE ──────────────────────────────────────────────────────────
window.handleGoogleLogin = async function () {
    try {
        const provider = new GoogleAuthProvider();
        const cred = await signInWithPopup(auth, provider);
        await establishServerSession(cred.user);
    } catch (e) {
        console.error("[Firebase Error]", e.code, e.message);
        showAuthError(mapFirebaseError(e.code));
    }
};

// ── LUPA PASSWORD ─────────────────────────────────────────────────────────
// (Sekarang full server-side lewat Laravel + OTP email, lihat forgot-password.blade.php)

// ── LOGOUT (dipanggil dari tombol Log Out) ───────────────────────────────
window.handleLogout = async function () {
    await signOut(auth);
    window.location.href = "/logout-server"; // hapus session Laravel juga
};

function mapFirebaseError(code) {
    const map = {
        "auth/email-already-in-use": "Email sudah terdaftar.",
        "auth/invalid-email": "Format email tidak valid.",
        "auth/weak-password": "Password minimal 6 karakter.",
        "auth/user-not-found": "Email tidak terdaftar.",
        "auth/wrong-password": "Password salah.",
        "auth/invalid-credential": "Email atau password salah.",
        "auth/popup-closed-by-user": "Login Google dibatalkan.",
    };
    return map[code] || "Terjadi kesalahan, coba lagi.";
}

function showAuthError(msg) {
    const el = document.getElementById("auth-error");
    if (el) {
        el.textContent = msg;
        el.classList.remove("hidden");
    }
}
function showAuthSuccess(msg) {
    const el = document.getElementById("auth-success");
    if (el) {
        el.textContent = msg;
        el.classList.remove("hidden");
    }
}
