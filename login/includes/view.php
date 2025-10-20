<?php

declare(strict_types=1);

// Get all errors from session (for login or signup) and clear them
function getErrors(string $type = 'login'): array {
    $key = $type === 'login' ? 'errors_login' : 'errors_signup';
    $errors = $_SESSION[$key] ?? [];
    unset($_SESSION[$key]); // remove errors so they don't show again
    return $errors;
}

// Show errors for a specific field
function error(string $field, array $errors): string {
    if (!empty($errors[$field])) {
        $output = '';
        // if multiple errors for the field, display all
        if (is_array($errors[$field])) {
            foreach ($errors[$field] as $msg) {
                $output .= '<p style="color:red">' . htmlspecialchars($msg) . '</p>';
            }
        } else {
            $output .= '<p style="color:red">' . htmlspecialchars($errors[$field]) . '</p>';
        }
        return $output;
    }
    return '';
}

// Show general error (like login failed)
function generalError(array $errors): string {
    if (!empty($errors['general'])) {
        return '<p style="color:red">' . htmlspecialchars($errors['general']) . '</p>';
    }
    return '';
}
