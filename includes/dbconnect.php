<?php

declare(strict_types=1);

require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/db.php';

spartan_load_dotenv(spartan_root() . '/.env');

try {
    $conn = spartan_connect();
} catch (RuntimeException $e) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo '<p style="font-family: sans-serif; padding: 2rem;">' . spartan_e($e->getMessage()) . '</p>';
    exit(1);
}
