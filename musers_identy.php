<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/includs/config.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/functions/functions.php';
if(!$myip) exit();

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Документ без названия</title>
</head>

<body>
<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>
<?php
if(empty($_POST['go'])) exit();
$query = qwe("SELECT * FROM `mailusers`");
foreach($query as $q)
{
	$identy = random_str(12);
	$muser_id = $q['mail_id'];
	qwe("UPDATE `mailusers` SET `identy` = '$identy' WHERE `mail_id` = '$muser_id'");
}
?>
</body>
</html>