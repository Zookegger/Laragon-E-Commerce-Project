<?php

class SessionHelper {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['username']);
    }

    public static function isAdmin() {
        self::startSession();
        return isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function getRole() {
        self::startSession();
        return $_SESSION['role'] ?? 'guest';
    }

    public static function getUsername() {
        self::startSession();
        return $_SESSION['username'] ?? 'guest';
    }

    public static function logout() {
        session_start();
        session_unset();
        session_destroy();
    }
}