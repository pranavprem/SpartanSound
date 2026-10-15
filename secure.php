<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/lib.php';
spartan_session_start();
if (!isset($_SESSION['user']) || !is_string($_SESSION['user']) || $_SESSION['user'] === '') {
    header('Location: index.php');
    exit;
}
$usernames = spartan_credential_usernames(spartan_root() . '/txt/credentials.txt');
?>
<!doctype html>
<html lang="en">
<!-- This website is built modifying styles and themes originally created by bootstrapmade-->

    <head>
        <?php require __DIR__ . "/includes/head.php" ?>

        <title>SpartanSound -- Secure</title>
    </head>
    <body>
        Hello, <?php echo spartan_e($_SESSION['user']); ?>
        <br/>
        Current website Users are:
        <br/>
        <?php
        if ($usernames === []) {
            echo "The user list is unavailable.";
        }
        foreach ($usernames as $username) {
            echo spartan_e($username);
            echo "<br/>\n";
        }
        ?>

        <a class="link animated fadeInUp delay-1s servicelink" href="includes/logout.php">Logout</a>

</body>
</html>
