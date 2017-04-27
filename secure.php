<?php session_start();
        ob_start();
            if (!isset($_SESSION["user"])){
                header("location:index.php");
                die();
            }
        ?>
<!doctype html>
<html>
<!-- This website is built modifying styles and themes originally created by bootstrapmade-->

    <head>
        <?php require "includes/head.php" ?>
        
        <title>SpartanSound -- Secure</title>
    </head>
    <body>
        Hello, <?php echo $_SESSION["user"]?>
        <br/>
        Current website Users are:
        <br/>
        <?php
            $userfile = fopen("txt/credentials.txt", "r");
            while(($line=fgets($userfile))!==false){
                    $user = explode(",", $line);
                    echo $user[0];
                    echo "<br/>";
                }
            fclose($userfile);    
        ?>
        
        <a class="link animated fadeInUp delay-1s servicelink" href="includes/logout.php">Logout</a>
        
</body>
</html>