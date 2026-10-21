<?php

declare(strict_types=1);

/**
 * Open the catalog database using DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, and DB_NAME.
 * DB_CONNECT_RETRIES lets Docker wait for MariaDB without slowing a normal failure.
 */
function spartan_connect(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $retries = (int) spartan_env('DB_CONNECT_RETRIES', '1');
    $retries = max(1, min(30, $retries));
    $host = spartan_env('DB_HOST', '127.0.0.1');
    $user = spartan_env('DB_USER', 'spartanuser');
    $password = spartan_env('DB_PASSWORD');
    $name = spartan_env('DB_NAME', 'spartansound');
    $port = (int) spartan_env('DB_PORT', '3306');
    if ($port < 1) {
        $port = 3306;
    }

    for ($attempt = 1; $attempt <= $retries; $attempt++) {
        try {
            $conn = new mysqli($host, $user, $password, $name, $port);
            $conn->set_charset('utf8mb4');
            return $conn;
        } catch (mysqli_sql_exception) {
            if ($attempt < $retries) {
                sleep(1);
            }
        }
    }
    throw new RuntimeException(
        'Database is unavailable. Start MariaDB and check DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME.'
    );
}

/**
 * @param list<int|string> $params
 * @return list<array<string, mixed>>
 */
function spartan_select(mysqli $conn, string $sql, array $params = []): array
{
    $result = $params === [] ? $conn->query($sql) : $conn->execute_query($sql, $params);
    if (!$result instanceof mysqli_result) {
        return [];
    }
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    return $rows;
}

/**
 * @param list<int|string> $params
 */
function spartan_execute(mysqli $conn, string $sql, array $params = []): void
{
    $result = $params === [] ? $conn->query($sql) : $conn->execute_query($sql, $params);
    if ($result instanceof mysqli_result) {
        $result->free();
    }
}

/**
 * @return list<array<string, mixed>>
 */
function spartan_products(mysqli $conn): array
{
    return spartan_select(
        $conn,
        'SELECT id, name, description, imgUrl, price, ud1, keywords, hits
         FROM products ORDER BY id'
    );
}

/**
 * @return list<array<string, mixed>>
 */
function spartan_top_products(mysqli $conn, int $limit = 5): array
{
    $limit = max(1, min(50, $limit));
    return spartan_select(
        $conn,
        'SELECT id, name, description, imgUrl, price, ud1, keywords, hits
         FROM products ORDER BY hits DESC, id ASC LIMIT ' . $limit
    );
}

/**
 * @return array<string, mixed>|null
 */
function spartan_find_product(mysqli $conn, int $id): ?array
{
    if ($id < 1) {
        return null;
    }
    $rows = spartan_select(
        $conn,
        'SELECT id, name, description, imgUrl, price, ud1, keywords, hits
         FROM products WHERE id = ?',
        [$id]
    );
    return $rows[0] ?? null;
}

function spartan_increment_hits(mysqli $conn, int $id): void
{
    if ($id < 1) {
        return;
    }
    spartan_execute($conn, 'UPDATE products SET hits = hits + 1 WHERE id = ?', [$id]);
}

/**
 * @param list<int> $ids
 * @return array<int, array<string, mixed>>
 */
function spartan_products_by_ids(mysqli $conn, array $ids): array
{
    $clean = [];
    foreach ($ids as $id) {
        $id = (int) $id;
        if ($id > 0) {
            $clean[] = $id;
        }
    }
    $clean = array_values(array_unique($clean));
    if ($clean === []) {
        return [];
    }
    $placeholders = implode(', ', array_fill(0, count($clean), '?'));
    $rows = spartan_select(
        $conn,
        'SELECT id, name, description, imgUrl, price, ud1, keywords, hits
         FROM products WHERE id IN (' . $placeholders . ')',
        $clean
    );
    $byId = [];
    foreach ($rows as $row) {
        $byId[(int) $row['id']] = $row;
    }
    return $byId;
}

/**
 * @param array<string, mixed> $input
 * @return list<array<string, mixed>>
 */
function spartan_search_users(mysqli $conn, array $input): array
{
    [$sql, $params] = spartan_user_search_parts($input);
    return spartan_select($conn, $sql, $params);
}

/**
 * @param array{
 *     firstname: string,
 *     lastname: string,
 *     email: string,
 *     homeaddress: string,
 *     homephone: string,
 *     cellphone: string
 * } $user
 */
function spartan_insert_user(mysqli $conn, array $user): void
{
    spartan_execute(
        $conn,
        'INSERT INTO users (firstname, lastname, email, homeaddress, homephone, cellphone)
         VALUES (?, ?, ?, ?, ?, ?)',
        [
            $user['firstname'],
            $user['lastname'],
            $user['email'],
            $user['homeaddress'],
            $user['homephone'],
            $user['cellphone'],
        ]
    );
}

/**
 * @return list<string>
 */
function spartan_user_first_names(mysqli $conn): array
{
    $rows = spartan_select($conn, 'SELECT firstname FROM users ORDER BY id');
    $names = [];
    foreach ($rows as $row) {
        $names[] = (string) $row['firstname'];
    }
    return $names;
}
