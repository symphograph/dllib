<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Генерация id</title>
</head>
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
if(!empty($_POST['set_ids']))
{
	include 'includs/config.php';
	$query = qwe("SELECT * FROM `crafts_35` WHERE `my_craft` = 1 AND `on_off` = 1 ORDER BY `craft_id`");
	$i = 20001;
	foreach($query as $v)
	{
		$craft_id_now = $v['craft_id'];
		$rec_name = $v['rec_name'];
		$dood_id = $v['dood_id'];
		$query_mats = qwe("SELECT * FROM `craft_materials` WHERE `craft_id` = '$craft_id_now'");
		foreach($query_mats as $mat)
		{
			
		}
		$i++;
		
	}
	echo 'Скрипт выполнен'; exit();
}
?>


<body>
Эта кнопка перепишет id для моих рецептов.
<form action="" method="post">
<input type="hidden" value="1" name="set_ids">
<input type="submit" value="Сгенерировать id">
</form>
</body>
</html>