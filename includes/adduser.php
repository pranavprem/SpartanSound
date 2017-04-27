<?php require "dbconnect.php"?>
<?php 

    $sql = "INSERT INTO users (firstname,lastname,email,homeaddress,homephone,cellphone) VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["homeaddress"]."', '".$_POST["homephone"]."', '".$_POST["cellphone"]."')";
    $conn->query($sql);
    $conn->close();
    header("location: ../index.php#users");
    
?>