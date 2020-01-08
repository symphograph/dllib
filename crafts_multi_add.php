<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Добавляем кучу рецептов</title>
</head>
<?php
require_once 'includs/ip.php';
if(!$myip) exit();
if(!empty($_POST['set_ids']))
{
	include 'includs/config.php';
	$query = qwe("SELECT * FROM `items` WHERE `description` LIKE '%с помощью сферы призрачной энергии%'");
	$query_last_craft = qwe("SELECT * FROM `crafts` WHERE `my_craft` = 1 ORDER BY `craft_id` DESC LIMIT 1");
	foreach($query_last_craft as $lstcr)
	{
		$i = $lstcr['craft_id'];
	}
	$my_craft = 1;
	$on_off = 1;
	foreach($query as $v)
	{
		
		//$craft_id_now = $v['craft_id'];
		$rec_name = $v['item_name'];
		$dood_id = '';
		$dood_name = '';
		$result_item_id = $v['item_id'];
		$result_item_name = $v['item_name'];
		$labor_need = 0;
		$profession = '';
		$prof_need = '';
		$result_amount = 1;
		$dood_group = '';
		$craft_id = $i;
		qwe("INSERT INTO `crafts` 
		(`craft_id`, `rec_name`, `dood_id`, `dood_name`, `result_item_id`, `result_item_name`, `labor_need`, `profession`, `prof_need`, `result_amount`, `my_craft`, `on_off`)
		VALUES 
		('$craft_id', '$rec_name', '$dood_id', '$dood_name', '$result_item_id', '$result_item_name', '$labor_need', '$profession', '$prof_need', '$result_amount', '1', '$on_off')
		");
		
		
			$mat_id = 43807;
			$mat_name = $mat['item_name'];
			$mater_need = $mat['mater_need'];
			qwe("REPLACE INTO `craft_materials` 
			(`craft_id`, `item_id`, `result_item_id`, `mater_need`, `item_name`, `result_item_name`)
			VALUES 
			('$craft_id', '$mat_id', '$result_item_id', '1', 'Сфера призрачной энергии', '$result_item_name')
			");
			
		
		$i++;
		
	}
	echo 'Скрипт выполнен'; exit();
}
?>


<body>
Эта кнопка добавляет кучу рецептов.<br>
Проверь имена таблиц!
<form action="" method="post">
<input type="hidden" value="1" name="set_ids">
<input type="submit" value="Импортировать">
</form>
</body>
</html>