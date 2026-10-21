<?php

declare(strict_types=1);

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/Server.php';
require_once __DIR__ . '/HttpClient.php';
require_once __DIR__ . '/../includes/lib.php';
require_once __DIR__ . '/../includes/db.php';

$test = new \SpartanSound\TestRunner();
spartan_load_dotenv(spartan_root() . '/.env');

try {
    $conn = spartan_connect();
    $products = spartan_products($conn);
    $users = spartan_search_users($conn, []);
} catch (Throwable $e) {
    $message = $e->getMessage();
    $password = spartan_env('DB_PASSWORD');
    if ($password !== '') {
        $message = str_replace($password, '[redacted]', $message);
    }
    fwrite(STDERR, "Integration tests need sql/schema.sql loaded in the configured database.\n$message\n");
    exit(1);
}

$test->same(10, count($products), 'seed has ten products');
$test->check(count($users) >= 20, 'seed has at least twenty users');

$nasty = "Robert'); DROP TABLE users;--";
$conn->execute_query('DELETE FROM users WHERE email = ?', ['integration@example.com']);
spartan_insert_user($conn, [
    'firstname' => 'Ada <script>',
    'lastname' => $nasty,
    'email' => 'integration@example.com',
    'homeaddress' => '1 Test St',
    'homephone' => '408-555-0199',
    'cellphone' => '408-555-0199',
]);
$found = spartan_search_users($conn, ['name' => $nasty]);
$test->same(1, count($found), 'a hostile name is stored as data');
$test->same($nasty, (string) $found[0]['lastname'], 'the hostile name round-trips');
$test->same(10, count(spartan_products($conn)), 'the product table is still there');

$server = new \SpartanSound\SpartanServer('127.0.0.1', 8876, spartan_root());
$cookie = spartan_root() . '/tests/cookies.txt';
@unlink($cookie);
$http = new \SpartanSound\SpartanHttp($server->baseUrl(), $cookie);

try {
    $server->start();

    $home = $http->request('GET', '/index.php');
    $test->same(200, $home['status'], 'home page returns 200');
    $test->check(str_contains($home['body'], 'SpartanSound'), 'home page names the shop');
    $test->check(str_contains($home['body'], 'Three Horsemen'), 'home page lists a seeded track');
    $test->check(str_contains($home['body'], 'pranavprem93@gmail.com'), 'contacts still come from the text file');
    $test->check(str_contains($home['body'], 'Ada &lt;script&gt;'), 'user html is escaped');
    $test->check(str_contains($home['body'], 'Robert&#039;); DROP TABLE users;--'), 'user quotes are escaped');

    $missing = $http->request('GET', '/viewproduct.php?id=99999');
    $test->same(200, $missing['status'], 'unknown product does not 500');
    $test->check(str_contains($missing['body'], 'not in the catalog'), 'unknown product explains itself');

    $before = spartan_find_product($conn, 1);
    $injected = $http->request('GET', '/viewproduct.php?id=' . rawurlencode('1 OR 1=1'));
    $test->same(200, $injected['status'], 'a query-string id does not 500');
    $test->check(str_contains($injected['body'], 'Three Horsemen'), 'a non-numeric tail still selects that one id');
    $test->check(str_contains($injected['body'], 'audio/3horsemen.mp3'), 'the track page points at its audio file');
    $test->check(!str_contains($injected['body'], 'Club Turntable'), 'the product page does not dump the catalog');
    $after = spartan_find_product($conn, 1);
    $test->check(is_array($before) && is_array($after), 'product 1 is still in the catalog');
    if (is_array($before) && is_array($after)) {
        $test->same((int) $before['hits'] + 1, (int) $after['hits'], 'opening a product counts one hit');
    }

    $poison = $http->request('GET', '/index.php', null, 'lastids=1%20OR%201%3D1%2C%3Cscript%3E');
    $test->same(200, $poison['status'], 'a hostile lastids cookie does not 500');
    $test->same(10, count(spartan_products($conn)), 'a hostile cookie does not change the catalog');

    $hidden = $http->request('GET', '/txt/credentials.txt');
    $test->same(404, $hidden['status'], 'the credential file is not downloadable');
    $test->check(!str_contains($hidden['body'], 'admin,admin'), 'the credential body is not in the 404');

    $css = $http->request('GET', '/css/style.css');
    $test->same(200, $css['status'], 'theme css is served');
    $test->check(str_contains($css['body'], '.portfolioContainer'), 'theme css is the knight stylesheet');

    $login = $http->request('POST', '/includes/loginvalidation.php', [
        'Username' => 'admin',
        'Password' => 'nope',
        'Login' => 'Login',
    ]);
    $test->same(302, $login['status'], 'a bad login redirects');
    $test->check(str_contains($login['headers'], 'login=failed'), 'a bad login explains the failure');

    $login = $http->request('POST', '/includes/loginvalidation.php', [
        'Username' => 'admin',
        'Password' => 'admin',
        'Login' => 'Login',
    ]);
    $test->same(302, $login['status'], 'demo admin login redirects');
    $test->check(str_contains($login['headers'], 'secure.php'), 'demo admin login heads to the secure page');
    $secure = $http->request('GET', '/secure.php');
    $test->same(200, $secure['status'], 'secure page loads for admin');
    $test->check(str_contains($secure['body'], 'Hello, admin'), 'secure page greets the session user');
    $test->check(str_contains($secure['body'], 'Mary'), 'secure page lists demo usernames');
    $test->check(!str_contains($secure['body'], 'Shadeslayer'), 'secure page does not print passwords');

    $added = $http->request('POST', '/includes/adduser.php', [
        'firstname' => 'Integration',
        'lastname' => 'Tester',
        'email' => 'integration-form@example.com',
        'homeaddress' => '2 Test St',
        'homephone' => '408-555-0142',
        'cellphone' => '408-555-0143',
    ]);
    $test->same(302, $added['status'], 'adding a user redirects home');
    $formUser = spartan_search_users($conn, ['email' => 'integration-form@example.com']);
    $test->same(1, count($formUser), 'the add-user form inserted a row');

    $blank = $http->request('GET', '/includes/adduser.php');
    $test->same(302, $blank['status'], 'GET does not insert a user');
    $test->check(str_contains($blank['headers'], 'user=missing'), 'a nameless add is rejected');
} finally {
    $server->stop();
    $conn->execute_query('DELETE FROM users WHERE email IN (?, ?)', [
        'integration@example.com',
        'integration-form@example.com',
    ]);
    @unlink($cookie);
    @unlink(spartan_root() . '/tests/server.log');
}

$test->finish('integration tests');
