<?php require "includes/dbconnect.php"?>
<?php
    parse_str($_SERVER['QUERY_STRING']);
    $result = $conn->query("SELECT * FROM products where id = ".$id.";");
    $prod = $result -> fetch_assoc();
    $hits = $prod["hits"] + 1;
    $conn->query("UPDATE products SET hits = ".$hits." WHERE id = ".$id.";");    
    $conn->close();
?>
<?php
    if(isset($_COOKIE["lastids"])){
        if(explode(",",$_COOKIE["lastids"])[0]!=$prod["id"]){
            setcookie("lastids",$prod["id"].",".$_COOKIE["lastids"],time() + (86400 * 30));    
        }
        
    }
    else{
        setcookie("lastids", $prod["id"], time() + (86400 * 30));
    }
?>

<html>
    <head>
        <?php require "includes/head.php" ?>
        <title><?php echo $prod["name"]?></title>
        <?php ob_start(); ?>
    </head>
    
    <body>
        
        <nav class="main-nav-outer" id="test">
	<div class="container">
        <ul class="main-nav">
            <li><a href="./index.php#about">ABOUT</a></li>
            <li><a href="./index.php#services">SERVICES</a></li>
            <li><a href="./index.php#products">PRODUCTS</a></li>
            <li class="small-logo"><a href="./index.php#header"><img class="headerlogo" style="width:50%" src="img/SpartanMusicBlackLogo.png" alt=""></a></li>
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
                <h2><?php echo $prod["name"]?></h2>
                <h3><?php echo $prod["description"]?></h3>
                <?php echo "<img style='width:50%' src = '".$prod["imgUrl"]."'>"; ?>
                   <br/>
                   <br/>
                <h3>$ <?php echo $prod["price"]?></h3>
                <?php 
                    if($prod["ud1"]!=""){
                        echo "<audio controls><source src='".$prod["ud1"]."'></audio>";
                    }
                   ?>
                <h4>Keywords: <?php echo $prod["keywords"]?></h4>
                </center>
            </div>
        </section>
    </body>
</html>