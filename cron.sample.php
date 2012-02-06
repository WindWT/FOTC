<?php
$url = "YOURDOMAIN/YOURFOLDER/index.php?work=1";

if (empty ( $url )) {
	exit ( '请先配置cron.php' );
}

$curlHandler = curl_init ( $url );

if (isset ( $_GET ['echo'] )) {
	curl_setopt ( $curlHandler, CURLOPT_RETURNTRANSFER, true );
}

curl_exec ( $curlHandler );
curl_close ( $curlHandler );

?>