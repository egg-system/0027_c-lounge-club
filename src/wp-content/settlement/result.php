<!--SuccessOK!-->

<!-- テレコムの仕様書からそのままコピーしたサンプルソースです -->

<html><head>
<title>決済データ送信先URL</title>
</head><body>
<?php
$cdate = date("Y/m/d H:i:s", date("U"));
$hpadrs = $_SERVER["REQUEST_URI"];
$refer = $_SERVER["HTTP_REFERER"];
$agent = $_SERVER["HTTP_USER_AGENT"];
$ipadrs = $_SERVER["REMOTE_ADDR"];
$filnam = "settlement_log.cgi";
$fp = fopen($filnam,"a");
fputs($fp,"$cdate ip=$ipadrs $hpadrs $refer$agent¥n"); fclose($fp);
?>
</body>
</html>