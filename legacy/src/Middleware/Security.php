<?php

namespace Nosde\ProyectoIglesia\Middleware;

/**
 * Security Middleware
 * Handles CSRF Protection and general security helpers.
 */
class Security
{
    private static $tokenName = 'csrf_token';

    /**
     * Generates a secure CSRF token if it doesn't exist.
     * @return string
     */
    public static function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::$tokenName];
    }

    /**
     * Validates the CSRF token from headers or POST data.
     * @return bool
     */
    public static function validateCSRFToken(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $storedToken = $_SESSION[self::$tokenName] ?? null;
        if (!$storedToken) {
            return false;
        }

        // Check Header (Standard for Fetch/Ajax)
        $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if ($headerToken && hash_equals($storedToken, $headerToken)) {
            return true;
        }

        // Check POST data
        $postToken = $_POST[self::$tokenName] ?? null;
        if ($postToken && hash_equals($storedToken, $postToken)) {
            return true;
        }

        return false;
    }

    /**
     * Helper to exit with unauthorized error
     */
    public static function unauthorized()
    {
        header('Content-Type: application/json', true, 403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Seguridad: Token CSRF inválido o ausente.'
        ]);
        exit;
    }
}
