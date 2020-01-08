
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
?>
<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>
<?php if(empty($_POST['go'])) exit();?>
<title>Документ без названия</title>
</head>

<body>

<?php
include 'includs/config.php';
$query = qwe("SELECT * FROM `items` WHERE `description` LIKE '%Чтобы использовать этот предмет как материал для изготовления снаряжения, его качество должно быть не ниже%'");
$forups = ['необычного', 'редкого', 'уникального', 'эпического', 'легендарного', 'реликвии', 'эпохи чудес'];
foreach($query as $q)
{
	$item_name = $q['item_name'];
	$description = $q['description'];
	$item_id = $q['item_id'];
	preg_match_all('#Чтобы использовать этот предмет как материал для изготовления снаряжения, его качество должно быть не ниже (.+?).<br>#is', $description, $arr);
	$forup = $arr[1][0];
	$grade = array_search($forup,$forups) + 2;
	echo '<p>'.$item_id.' | '.$item_name.' | '.$grade.' | '.$forup.'</p>';
	qwe("UPDATE `items` SET `forup_grade` = '$grade' WHERE `item_id` = '$item_id'");

}

?>
</body>
</html>