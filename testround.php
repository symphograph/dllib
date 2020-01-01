<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
<?php
	include_once 'includs/config.php';
$idquer="select max(`mail_id`) from `mailusers`";
			$query= qwe($idquer);
	foreach($query as $key){
		$uid = $key['max(`mail_id`)'];}
	echo $uid;
?>
</body>
</html>