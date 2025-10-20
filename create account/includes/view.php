<?php

// Safely fetch old input value
function old(string $key): string {
    return htmlspecialchars($_SESSION['old'][$key] ?? '');
}

// Safely fetch and display an error for a field
function error(string $key): string {
    if (!empty($_SESSION['errors'][$key])) {
        return '<p style="color:red">' . htmlspecialchars($_SESSION['errors'][$key]) . '</p>';
    }
    return '';
}
