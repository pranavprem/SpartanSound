<?php

declare(strict_types=1);

require_once __DIR__ . '/dbconnect.php';

header('Content-Type: text/plain; charset=UTF-8');

$names = [];
foreach (spartan_user_first_names($conn) as $name) {
    $clean = str_replace([',', "\r", "\n"], '', $name);
    if ($clean !== '') {
        $names[] = $clean;
    }
}
echo implode(',', $names);
