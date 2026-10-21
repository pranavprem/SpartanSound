<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/lib.php';
require_once __DIR__ . '/../includes/db.php';

spartan_load_dotenv(spartan_root() . '/.env');

try {
    $conn = spartan_connect();
    $sql = file_get_contents(spartan_root() . '/sql/schema.sql');
    if (!is_string($sql) || $sql === '') {
        throw new RuntimeException('sql/schema.sql is missing.');
    }
    $conn->multi_query($sql);
    do {
        $result = $conn->store_result();
        if ($result instanceof mysqli_result) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
} catch (Throwable $e) {
    $message = $e->getMessage();
    $password = spartan_env('DB_PASSWORD');
    if ($password !== '') {
        $message = str_replace($password, '[redacted]', $message);
    }
    fwrite(STDERR, "Could not load sql/schema.sql.\n$message\n");
    exit(1);
}

echo "schema loaded\n";
