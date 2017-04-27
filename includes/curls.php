<html>
    
    <head>
<?php require "head.php"?>
    </head>
<body>
<section class="main-section alabaster" id="tops">
	<div class="container fullsize">
<?php

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "http://www.pranavprem.com/spartansound/includes/expose.php");
curl_setopt($curl_handle, CURLOPT_HEADER, 0);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,true);
$contents = curl_exec($curl_handle);
curl_setopt($curl_handle, CURLOPT_URL, "http://divyaatv.com/zyro/fetchuser.php");
$contents = $contents.",".curl_exec($curl_handle);
curl_setopt($curl_handle, CURLOPT_URL, "http://hireharry.com/php/userlist/");
$contents = $contents.",".curl_exec($curl_handle);
curl_setopt($curl_handle, CURLOPT_URL, "http://helloparag.com/flyby/createDBConn.php");
$contents = $contents.",".curl_exec($curl_handle);
curl_setopt($curl_handle, CURLOPT_URL, "http://arunram.in/valarshopoholics/displayusers.php");
$contents = $contents.",".curl_exec($curl_handle);

#echo "<br/>";
curl_close($curl_handle);

foreach (explode(",",$contents) as $content) {
    echo $content."<br/>";
}

?>
        
    </div>
</section>
</body>
</html>