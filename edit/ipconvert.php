<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
<?php
$myip = $_SERVER['REMOTE_ADDR'];
if($myip !== '37.194.65.246'){
echo '<center>Чужой</center>';
exit();};

include_once '../includs/config.php';
$query = qwe("SELECT `ip` FROM `packssbor`");
 foreach($query as $key2){
	$ip = $key2['ip'];
	$iplong = ip2long($ip);
	qwe("UPDATE `packssbor` SET `ip2long` = '$iplong' WHERE `ip` = '$ip'");
	};

?>
</body>
</html>