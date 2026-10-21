<?php

declare(strict_types=1);

require_once __DIR__ . '/dbconnect.php';

$fields = ['firstname', 'lastname', 'email', 'homeaddress', 'homephone', 'cellphone'];
$input = [];
foreach ($fields as $field) {
    $input[$field] = trim((string) ($_POST[$field] ?? ''));
}

$posted = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
if (!$posted || $input['firstname'] === '' || $input['lastname'] === '') {
    header('Location: ../index.php?user=missing#users');
    exit;
}

spartan_insert_user($conn, $input);
header('Location: ../index.php#users');
exit;
