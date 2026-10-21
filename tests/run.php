<?php

declare(strict_types=1);

require_once __DIR__ . '/TestRunner.php';
require_once __DIR__ . '/../includes/lib.php';

$test = new \SpartanSound\TestRunner();
$root = spartan_root();

$test->same('4', spartan_remember_ids(null, 4), 'first visit stores the product id');
$test->same('4', spartan_remember_ids('4', 4), 'opening the current product again does not duplicate it');
$test->same('6,4,5', spartan_remember_ids('4,5', 6), 'a new product is prepended');
$test->same('6,4,5', spartan_remember_ids('6,4,5', 6), 'the most recent product stays put');
$test->same('5,4', spartan_remember_ids('abc,4', 5), 'non-numeric cookie junk is dropped');
$test->same('3,1', spartan_remember_ids('1,2,3', 3, 2), 'the cookie is capped');
$test->same('4', spartan_remember_ids('4,abc', 4), 'a clean reread drops junk without duplicating the id');

$test->same(
    [['id' => 1, 'views' => 2], ['id' => 2, 'views' => 1], ['id' => 3, 'views' => 1]],
    spartan_top_viewed_ids('1,2,1,3'),
    'most viewed ids keep first-seen order on a tie'
);
$test->same(
    [['id' => 2, 'views' => 1], ['id' => 1, 'views' => 1]],
    spartan_top_viewed_ids('2,1'),
    'equal counts prefer the earlier id'
);
$test->same(5, count(spartan_top_viewed_ids('1,2,3,4,5,6', 5)), 'top viewed stops at five');
$test->same([], spartan_top_viewed_ids('nope,,'), 'junk cookies rank nothing');

$test->same([3, 1, 3, 2, 9], spartan_recent_ids('3,1,3,2,9,8'), 'recent ids keep repeats and stop at five');
$test->same([4], spartan_recent_ids('xx,4,yy'), 'recent ids ignore junk');

[$sql, $params] = spartan_user_search_parts(['name' => '', 'email' => 'mary@example.com', 'phone' => '']);
$test->same(
    'SELECT firstname, lastname, email, homeaddress, homephone, cellphone FROM users WHERE email LIKE ? ORDER BY id',
    $sql,
    'email-only search ignores blank name fields'
);
$test->same(['%mary@example.com%'], $params, 'email search is a bound like pattern');

[$sql, $params] = spartan_user_search_parts(['name' => 'Ada', 'phone' => '555']);
$test->check(str_contains($sql, ' OR '), 'combined search ORs the filled fields');
$test->check(!str_contains($sql, "firstname = ''"), 'blank names are not part of the query');
$test->same(['%Ada%', '%Ada%', '%555%', '%555%'], $params, 'name and phone each bind two columns');

[$sql, $params] = spartan_user_search_parts([]);
$test->check(str_starts_with($sql, 'SELECT '), 'an empty search lists users');
$test->same([], $params, 'an empty search has no bound values');
$test->same('%100\\%%', spartan_like('100%'), 'like wildcards in the query are escaped');

$rows = [
    ['keywords' => 'alt rock, jazz'],
    ['keywords' => 'rock, jazz'],
];
$test->same(
    ['alt_rock', 'jazz', 'rock'],
    spartan_collect_keywords($rows),
    'keyword filters keep rock when another tag contains those letters'
);
$test->same(['hip_hop'], spartan_keyword_tokens('hip hop'), 'spaces in keywords become class tokens');
$test->same('live', spartan_keyword_token('live!'), 'keyword tokens drop characters that would break CSS');

$creds = $root . '/tests/tmp-credentials.txt';
file_put_contents($creds, "# comment, with comma\nadmin,admin\nAda,secret\n");
$test->check(spartan_credentials_match($creds, 'admin', 'admin'), 'demo admin password matches');
$test->check(!spartan_credentials_match($creds, 'admin', 'Admin'), 'password match stays case sensitive');
$test->check(!spartan_credentials_match($creds, 'admin', 'nope'), 'a wrong password does not match');
$test->same(['admin', 'Ada'], spartan_credential_usernames($creds), 'comment lines are not users');
unlink($creds);

$shipped = spartan_credential_usernames($root . '/txt/credentials.txt');
$test->check(in_array('admin', $shipped, true), 'shipped demo file still has admin');
$test->check(
    spartan_credentials_match($root . '/txt/credentials.txt', 'admin', 'admin'),
    'shipped demo admin account still authenticates'
);

$test->same(
    ['Mary', 'John'],
    spartan_peer_names("Mary, John,\n<html>parked page</html>, " . str_repeat('A', 81)),
    'peer responses drop html and overlong chunks'
);
$peers = spartan_peer_urls();
$test->same(4, count($peers), 'the four classmate curl endpoints are still listed');
foreach ($peers as $peer) {
    $test->check(str_starts_with($peer, 'http://'), 'classmate endpoints stay on their original http urls');
}
$test->same([], spartan_curl_texts([]), 'curl helper does nothing with an empty list');

$test->check(spartan_request_is_private('/txt/credentials.txt'), 'credential file is private');
$test->check(spartan_request_is_private('/sql/schema.sql'), 'schema is private');
$test->check(spartan_request_is_private('/../txt/credentials.txt'), 'path traversal is private');
$test->check(spartan_request_is_private('/%2e%2e/txt/credentials.txt'), 'encoded traversal is private');
$test->check(spartan_request_is_private('/.env'), 'env file is private');
$test->check(!spartan_request_is_private('/index.php'), 'home page is public');
$test->check(!spartan_request_is_private('/css/style.css'), 'theme css is public');
$test->check(!spartan_request_is_private('/audio/Pranav.mp3'), 'audio is public');
$test->check(!spartan_request_is_private('/includes/loginvalidation.php'), 'login endpoint stays reachable');

$banned = 'wuba' . 'lubadubdub';
$directory = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS);
$filter = new RecursiveCallbackFilterIterator(
    $directory,
    static function (SplFileInfo $current): bool {
        return !in_array($current->getFilename(), ['.git', 'vendor', 'node_modules'], true);
    }
);
$binary = ['mp3', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'woff', 'ttf', 'eot', 'otf', 'svg'];
foreach (new RecursiveIteratorIterator($filter) as $file) {
    if (!$file->isFile() || $file->getSize() > 500000) {
        continue;
    }
    if (in_array(strtolower($file->getExtension()), $binary, true)) {
        continue;
    }
    $contents = file_get_contents($file->getPathname());
    if (is_string($contents) && str_contains($contents, $banned)) {
        $test->check(false, 'retired database password found in ' . $file->getPathname());
    }
}
$test->check(
    !str_contains((string) file_get_contents($root . '/includes/dbconnect.php'), 'mysqli'),
    'the page bootstrap does not open the database with an inline driver call'
);

$schema = (string) file_get_contents($root . '/sql/schema.sql');
$test->check(str_contains($schema, 'CREATE TABLE users'), 'schema creates users');
$test->check(str_contains($schema, 'CREATE TABLE products'), 'schema creates products');
$test->check(substr_count($schema, "example.com") === 20, 'schema seeds twenty users');
$test->check(substr_count($schema, "img/") === 10, 'schema seeds ten products');

$envFile = $root . '/tests/tmp.env';
file_put_contents($envFile, "SPARTAN_UNIT_ONLY=from-file\n# comment\nBAD LINE\n");
putenv('SPARTAN_UNIT_ONLY');
unset($_ENV['SPARTAN_UNIT_ONLY']);
spartan_load_dotenv($envFile);
$test->same('from-file', spartan_env('SPARTAN_UNIT_ONLY'), 'dotenv loads a missing variable');
putenv('SPARTAN_UNIT_ONLY=already');
$_ENV['SPARTAN_UNIT_ONLY'] = 'already';
file_put_contents($envFile, "SPARTAN_UNIT_ONLY=from-file-again\n");
spartan_load_dotenv($envFile);
$test->same('already', spartan_env('SPARTAN_UNIT_ONLY'), 'dotenv does not override real environment');
putenv('SPARTAN_UNIT_ONLY');
unset($_ENV['SPARTAN_UNIT_ONLY']);
unlink($envFile);

$test->same('&lt;b&gt;', spartan_e('<b>'), 'output is escaped');

$test->finish('unit tests');
