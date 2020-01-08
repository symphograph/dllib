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
	$i = 1000000;
	$my_craft = 1;
	$on_off = 1;
	foreach($query as $v)
	{
		$craft_id_now = $v['craft_id'];
		$rec_name = $v['rec_name'];
		$dood_id = $v['dood_id'];
		$dood_name = $v['dood_id'];
		$result_item_id = $v['result_item_id'];
		$result_item_name = $v['result_item_name'];
		$labor_need = $v['labor_need'];
		$profession = $v['profession'];
		$prof_need = $v['prof_need'];
		$result_amount = $v['result_amount'];
		$dood_group = $v['dood_group'];
		$craft_id = $i;
		qwe("REPLACE INTO `crafts` 
		(`craft_id`, `rec_name`, `dood_id`, `dood_name`, `result_item_id`, `result_item_name`, `labor_need`, `profession`, `prof_need`, `result_amount`, `my_craft`, `on_off`)
		VALUES 
		('$craft_id', '$rec_name', '$dood_id', '$dood_name', '$result_item_id', '$result_item_name', '$labor_need', '$profession', '$prof_need', '$result_amount', '1', '1')
		");
		$query_mats = qwe("SELECT * FROM `craft_materials_35` WHERE `craft_id` = '$craft_id_now'");
		foreach($query_mats as $mat)
		{
			$mat_id = $mat['item_id'];
			$mat_name = $mat['item_name'];
			$mater_need = $mat['mater_need'];
			qwe("REPLACE INTO `craft_materials` 
			(`craft_id`, `item_id`, `result_item_id`, `mater_need`, `item_name`, `result_item_name`)
			VALUES 
			('$craft_id', '$mat_id', '$result_item_id', '$mater_need', '$mat_name', '$result_item_name')
			");
			
		}
		$i++;
		
	}
	echo 'Скрипт выполнен'; exit();
}
?>


<body>
Эта кнопка импортирует рецепты и материалы из старой базы в новую.<br>
Проверь имена таблиц!
<form action="" method="post">
<input type="hidden" value="1" name="set_ids">
<input type="submit" value="Импортировать">
</form>
</body>
</html>