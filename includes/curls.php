<?php require "head.php"?>
<?php

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_URL, "localhost/spartansound/includes/expose.php");
curl_setopt($curl_handle, CURLOPT_HEADER, 0);
curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,true);
$contents = curl_exec($curl_handle);

#echo "<br/>";
curl_close($curl_handle);

foreach (explode(",",$contents) as $content) {
    echo $content."<br/>";
}

?>