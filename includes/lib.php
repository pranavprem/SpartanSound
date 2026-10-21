<?php

declare(strict_types=1);

/**
 * Project root, independent of the process working directory.
 */
function spartan_root(): string
{
    return dirname(__DIR__);
}

/**
 * Load KEY=VALUE lines from a .env file without overriding existing variables.
 */
function spartan_load_dotenv(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $eq = strpos($line, '=');
        if ($eq === false) {
            continue;
        }
        $key = trim(substr($line, 0, $eq));
        $value = trim(substr($line, $eq + 1));
        if ($key === '' || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key) !== 1) {
            continue;
        }
        $quoted = (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"));
        if ($quoted) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) !== false) {
            continue;
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
    }
}

function spartan_env(string $key, string $default = ''): string
{
    if (array_key_exists($key, $_ENV)) {
        return (string) $_ENV[$key];
    }
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return (string) $value;
}

function spartan_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function spartan_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_save_path(sys_get_temp_dir());
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * @return array{username: string, password: string}|null
 */
function spartan_parse_credential_line(string $line): ?array
{
    $line = rtrim($line, "\r\n");
    if ($line === '' || str_starts_with($line, '#')) {
        return null;
    }
    $parts = explode(',', $line, 2);
    if (count($parts) < 2 || $parts[0] === '' || $parts[1] === '') {
        return null;
    }
    return ['username' => $parts[0], 'password' => $parts[1]];
}

function spartan_credentials_match(string $file, string $username, string $password): bool
{
    if (!is_file($file)) {
        return false;
    }
    $handle = fopen($file, 'rb');
    if ($handle === false) {
        return false;
    }
    $matched = false;
    while (($line = fgets($handle)) !== false) {
        $cred = spartan_parse_credential_line($line);
        if ($cred === null) {
            continue;
        }
        $userOk = hash_equals($cred['username'], $username);
        $passOk = hash_equals($cred['password'], $password);
        if ($userOk && $passOk) {
            $matched = true;
            break;
        }
    }
    fclose($handle);
    return $matched;
}

/**
 * @return list<string>
 */
function spartan_credential_usernames(string $file): array
{
    if (!is_file($file)) {
        return [];
    }
    $lines = file($file);
    if ($lines === false) {
        return [];
    }
    $names = [];
    foreach ($lines as $line) {
        $cred = spartan_parse_credential_line($line);
        if ($cred !== null) {
            $names[] = $cred['username'];
        }
    }
    return $names;
}

/**
 * Prepend a product id unless it is already the most recent one.
 * Non-numeric junk is dropped. The list is capped so the cookie cannot grow forever;
 * the cap stays large enough for the "most viewed" tally.
 */
function spartan_remember_ids(?string $existing, int $id, int $limit = 100): string
{
    $limit = max(1, $limit);
    $ids = [];
    if ($existing !== null && $existing !== '') {
        foreach (explode(',', $existing) as $part) {
            $part = trim($part);
            if ($part !== '' && ctype_digit($part)) {
                $ids[] = $part;
            }
        }
    }
    if ($id < 1) {
        return implode(',', array_slice($ids, 0, $limit));
    }
    if ($ids !== [] && (int) $ids[0] === $id) {
        return implode(',', array_slice($ids, 0, $limit));
    }
    array_unshift($ids, (string) $id);
    return implode(',', array_slice($ids, 0, $limit));
}

/**
 * Five (or fewer) most frequent ids. Ties keep the id that appeared first.
 *
 * @return list<array{id: int, views: int}>
 */
function spartan_top_viewed_ids(string $cookieValue, int $limit = 5): array
{
    $limit = max(1, $limit);
    $counts = [];
    foreach (explode(',', $cookieValue) as $part) {
        $part = trim($part);
        if ($part === '' || !ctype_digit($part)) {
            continue;
        }
        $counts[$part] = ($counts[$part] ?? 0) + 1;
    }
    $ranked = [];
    while (count($ranked) < $limit && $counts !== []) {
        $max = 0;
        $maxId = null;
        foreach ($counts as $key => $value) {
            if ($value > $max) {
                $max = $value;
                $maxId = (int) $key;
            }
        }
        if ($maxId === null || $max === 0) {
            break;
        }
        $ranked[] = ['id' => $maxId, 'views' => $max];
        unset($counts[(string) $maxId]);
    }
    return $ranked;
}

/**
 * Most recent ids, including repeats, in cookie order.
 *
 * @return list<int>
 */
function spartan_recent_ids(string $cookieValue, int $limit = 5): array
{
    $limit = max(1, $limit);
    $ids = [];
    foreach (explode(',', $cookieValue) as $part) {
        $part = trim($part);
        if ($part === '' || !ctype_digit($part)) {
            continue;
        }
        $ids[] = (int) $part;
        if (count($ids) >= $limit) {
            break;
        }
    }
    return $ids;
}

function spartan_like(string $value): string
{
    $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    return '%' . $escaped . '%';
}

/**
 * Search uses only the fields the visitor filled in, combined with OR,
 * matching the labels on the search form.
 *
 * @param array<string, mixed> $input
 * @return array{0: string, 1: list<string>}
 */
function spartan_user_search_parts(array $input): array
{
    $columns = 'firstname, lastname, email, homeaddress, homephone, cellphone';
    $clauses = [];
    $params = [];
    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $phone = trim((string) ($input['phone'] ?? ''));
    if ($name !== '') {
        $clauses[] = '(firstname LIKE ? OR lastname LIKE ?)';
        $like = spartan_like($name);
        $params[] = $like;
        $params[] = $like;
    }
    if ($email !== '') {
        $clauses[] = 'email LIKE ?';
        $params[] = spartan_like($email);
    }
    if ($phone !== '') {
        $clauses[] = '(homephone LIKE ? OR cellphone LIKE ?)';
        $like = spartan_like($phone);
        $params[] = $like;
        $params[] = $like;
    }
    if ($clauses === []) {
        return ["SELECT $columns FROM users ORDER BY id", []];
    }
    $sql = "SELECT $columns FROM users WHERE " . implode(' OR ', $clauses) . ' ORDER BY id';
    return [$sql, $params];
}

function spartan_keyword_token(string $keyword): string
{
    $token = str_replace(' ', '_', trim($keyword));
    $token = preg_replace('/[^A-Za-z0-9_-]/', '', $token);
    return $token ?? '';
}

/**
 * @return list<string>
 */
function spartan_keyword_tokens(string $keywords): array
{
    $tokens = [];
    $parts = preg_split('/\s*,\s*/', $keywords) ?: [];
    foreach ($parts as $part) {
        $token = spartan_keyword_token($part);
        if ($token !== '' && !in_array($token, $tokens, true)) {
            $tokens[] = $token;
        }
    }
    return $tokens;
}

/**
 * Unique filter tokens in first-seen order. Matching is by whole keyword,
 * so "rock" still appears when another product is tagged "alt rock".
 *
 * @param list<array<string, mixed>> $rows
 * @return list<string>
 */
function spartan_collect_keywords(array $rows): array
{
    $tokens = [];
    foreach ($rows as $row) {
        foreach (spartan_keyword_tokens((string) ($row['keywords'] ?? '')) as $token) {
            if (!in_array($token, $tokens, true)) {
                $tokens[] = $token;
            }
        }
    }
    return $tokens;
}

/**
 * Classmate endpoints from the 2017 curl exercise. The old page also curled
 * this site's own expose.php on pranavprem.com; local names now come from
 * the database instead of that dead host.
 *
 * @return list<string>
 */
function spartan_peer_urls(): array
{
    return [
        'http://divyaatv.com/zyro/fetchuser.php',
        'http://hireharry.com/php/userlist/',
        'http://helloparag.com/flyby/createDBConn.php',
        'http://arunram.in/valarshopoholics/displayusers.php',
    ];
}

/**
 * Split a comma-separated peer response and drop HTML pages from parked domains.
 *
 * @return list<string>
 */
function spartan_peer_names(string $csv): array
{
    $names = [];
    foreach (explode(',', $csv) as $piece) {
        $piece = str_replace(["\r", "\n"], '', $piece);
        if (str_contains($piece, '<') || str_contains($piece, '>')) {
            continue;
        }
        $piece = trim($piece);
        if ($piece === '' || strlen($piece) > 80) {
            continue;
        }
        $names[] = $piece;
    }
    return $names;
}

/**
 * Fetch several URLs at once. Failures and non-200 responses are omitted.
 *
 * @param list<string> $urls
 * @return array<string, string>
 */
function spartan_curl_texts(array $urls, int $timeout = 3): array
{
    if ($urls === [] || !function_exists('curl_multi_init')) {
        return [];
    }
    $multi = curl_multi_init();
    $handles = [];
    foreach ($urls as $url) {
        $handle = curl_init($url);
        if ($handle === false) {
            continue;
        }
        curl_setopt_array($handle, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        curl_multi_add_handle($multi, $handle);
        $handles[$url] = $handle;
    }
    $running = null;
    do {
        $status = curl_multi_exec($multi, $running);
        if ($running) {
            curl_multi_select($multi, 1.0);
        }
    } while ($running && $status === CURLM_OK);

    $bodies = [];
    foreach ($handles as $url => $handle) {
        $code = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $body = curl_multi_getcontent($handle);
        if (is_string($body) && $code >= 200 && $code < 300) {
            $bodies[$url] = $body;
        }
        curl_multi_remove_handle($multi, $handle);
    }
    return $bodies;
}

/**
 * True when a request path should not be served (credential files, env, tests).
 */
function spartan_request_is_private(string $path): bool
{
    $path = rawurldecode($path);
    $query = strpos($path, '?');
    if ($query !== false) {
        $path = substr($path, 0, $query);
    }
    $path = str_replace('\\', '/', $path);
    $path = '/' . ltrim($path, '/');
    while (str_contains($path, '//')) {
        $path = str_replace('//', '/', $path);
    }
    if (str_contains($path, '..')) {
        return true;
    }
    foreach (['txt', 'sql', 'tests', 'vendor'] as $prefix) {
        if ($path === '/' . $prefix || str_starts_with($path, '/' . $prefix . '/')) {
            return true;
        }
    }
    $blockedFiles = [
        '/.env',
        '/.env.example',
        '/composer.json',
        '/composer.lock',
        '/Dockerfile',
        '/docker-compose.yml',
        '/AGENTS.md',
        '/CLAUDE.md',
        '/phpcs.xml.dist',
        '/.gitignore',
        '/.editorconfig',
        '/.htaccess',
        '/.dockerignore',
    ];
    if (in_array($path, $blockedFiles, true) || str_starts_with($path, '/.')) {
        return true;
    }
    return false;
}
