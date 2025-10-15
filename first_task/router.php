<?php
// Simple router for PHP built-in server
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove query parameters that VS Code might add
$uri = strtok($uri, '?');

// If URI is empty or root, serve index.php
if ($uri === '/' || $uri === '') {
    include 'index.php';
    return true;
}

// If requesting a file that exists, let the server handle it
if (file_exists('.' . $uri)) {
    return false;
}

// Otherwise, serve index.php (SPA behavior)
include 'index.php';
return true;
?>