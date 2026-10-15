<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/dbconnect.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = spartan_find_product($conn, $id);
if ($product !== null) {
    spartan_increment_hits($conn, (int) $product['id']);
    $existing = isset($_COOKIE['lastids']) ? (string) $_COOKIE['lastids'] : null;
    $updated = spartan_remember_ids($existing, (int) $product['id']);
    if ($updated !== ($existing ?? '')) {
        setcookie('lastids', $updated, [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'samesite' => 'Lax',
        ]);
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php require __DIR__ . "/includes/head.php" ?>
        <title><?php echo $product === null ? 'SpartanSound' : spartan_e((string) $product['name']); ?></title>
    </head>

    <body>

        <nav class="main-nav-outer" id="test">
	<div class="container">
        <ul class="main-nav">
            <li><a href="./index.php#about">ABOUT</a></li>
            <li><a href="./index.php#services">SERVICES</a></li>
            <li><a href="./index.php#products">PRODUCTS</a></li>
            <li class="small-logo"><a href="./index.php#header"><img class="headerlogo" style="width:50%" src="img/SpartanMusicBlackLogo.png" alt="SpartanSound"></a></li>
            <li><a href="./index.php#news">NEWS</a></li>
            <li><a href="./index.php#contacts">CONTACTS</a></li>
            <li><a href="./index.php#users">USERS</a></li>

        </ul>
        <a class="res-nav_click" href="#"><i class="fa-bars"></i></a>
    </div>
</nav>
<section class="main-section alabaster" id="about">
	       <div class="container fullsize">
               <center>
                <?php if ($product === null) { ?>
                <h2>Product not found</h2>
                <p>That product is not in the catalog.</p>
                <a class="link" href="index.php#products">Back to products</a>
                <?php } else { ?>
                <h2><?php echo spartan_e((string) $product['name']); ?></h2>
                <h3><?php echo spartan_e((string) $product['description']); ?></h3>
                <img style="width:50%" src="<?php echo spartan_e((string) $product['imgUrl']); ?>" alt="<?php echo spartan_e((string) $product['name']); ?>">
                   <br/>
                   <br/>
                <h3>$ <?php echo spartan_e((string) $product['price']); ?></h3>
                <?php if ((string) $product['ud1'] !== '') { ?>
                <audio controls><source src="<?php echo spartan_e((string) $product['ud1']); ?>"></audio>
                <?php } ?>
                <h4>Keywords: <?php echo spartan_e((string) $product['keywords']); ?></h4>
                <?php } ?>
                </center>
            </div>
        </section>
    </body>
</html>
