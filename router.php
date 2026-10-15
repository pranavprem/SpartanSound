<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/lib.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!is_string($path)) {
    $path = '/';
}
if (spartan_request_is_private($path)) {
    http_response_code(404);
    echo 'Not found';
    return true;
}

return false;
