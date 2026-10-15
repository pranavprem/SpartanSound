<?php

declare(strict_types=1);

require_once __DIR__ . '/lib.php';
spartan_session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] !== '' ? $params['path'] : '/',
        $params['domain'],
        (bool) $params['secure'],
        true
    );
}
session_destroy();
header('Location: ../index.php');
exit;
