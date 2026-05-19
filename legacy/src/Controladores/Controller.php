<?php

namespace Nosde\ProyectoIglesia\Controladores;

use Nosde\ProyectoIglesia\Middleware\Security;

/**
 * Base Controller
 * Provides common utilities for all controllers, including sanitization.
 */
abstract class Controller
{
    /**
     * Sanitizes incoming JSON payload from php://input
     * @return array
     */
    protected function getSanitizedJson(): array
    {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            return [];
        }

        return $this->sanitizeArray($data);
    }

    /**
     * Recursively sanitizes an array
     * @param array $array
     * @return array
     */
    protected function sanitizeArray(array $array): array
    {
        $sanitized = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                // Remove tags and convert special characters to HTML entities
                $sanitized[$key] = is_string($value) ? htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8') : $value;
            }
        }
        return $sanitized;
    }

    /**
     * Standard JSON response helper
     */
    protected function jsonResponse($status, $message = '', $data = null, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Verifies CSRF before continuing
     */
    protected function validateCSRF()
    {
        if (!Security::validateCSRFToken()) {
            Security::unauthorized();
        }
    }
}
