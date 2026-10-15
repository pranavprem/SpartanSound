<?php

declare(strict_types=1);

require_once __DIR__ . '/lib.php';
spartan_session_start();

$username = (string) ($_POST['Username'] ?? '');
$password = (string) ($_POST['Password'] ?? '');
$posted = isset($_POST['Login']) && $username !== '' && $password !== '';
$credentials = spartan_root() . '/txt/credentials.txt';

if ($posted && spartan_credentials_match($credentials, $username, $password)) {
    session_regenerate_id(true);
    $_SESSION['user'] = $username;
    header('Location: ../secure.php');
    exit;
}

if (!$posted) {
    header('Location: ../index.php');
    exit;
}

header('Location: ../index.php?login=failed');
exit;
