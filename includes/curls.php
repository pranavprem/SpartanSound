<?php

declare(strict_types=1);

require_once __DIR__ . '/dbconnect.php';

$names = spartan_user_first_names($conn);
foreach (spartan_curl_texts(spartan_peer_urls()) as $body) {
    foreach (spartan_peer_names($body) as $name) {
        $names[] = $name;
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
<base href="../">
<?php require __DIR__ . "/head.php" ?>
    </head>
<body>
<section class="main-section alabaster" id="tops">
	<div class="container fullsize">
<?php
foreach ($names as $name) {
    echo spartan_e($name) . "<br/>\n";
}
?>

    </div>
</section>
</body>
</html>
