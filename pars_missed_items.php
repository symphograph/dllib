<meta charset="utf-8">
<?php
$start = microtime(true);
include 'includs/ip.php';
if(!$myip) exit();
?>
Не забудь сделать бэкап таблицы parsed_items!
Проверь  $start_id и $stop_id
<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>
<?php
if(empty($_POST['go'])) exit();
include 'includs/config.php';
include 'functions/pars_functs.php';


$start_id = 3332;
$id = $start_id;
$query = qwe("SELECT DISTINCT `item_id` FROM `craft_materials` WHERE `item_id` NOT in
(SELECT `item_id` FROM `items`)");
if($query)

	foreach($query as $mxitm)
	{
		$itm = $mxitm['item_id'];
	qwe("UPDATE `parsed_last` SET `item_id` = '$itm' WHERE `id` = 1");
	echo '<hr>';
	$plink = 'http://archeagedatabase.net/ru/item/'.$itm;
	$somepage = curl($plink);
	$is_trade_npc = 0;
	
	//exit();
	if(!$somepage) echo 'error';
	preg_match_all('#<td colspan="2">ID:(.+?)<div class="addon_info">#is', $somepage, $arr);
		
	if(!$arr[0][0]) {echo 'Предмет пуст<hr>'; continue;}
	else
	$table = $arr[0][0];
	$price_type = '';
	
	$item_id = AboutCraft('#ID: (.+?)td>#is', 'digits', $table);
	echo '<p>Id: '.$itm.' | ';

	if($item_id < 1) {echo 'Предмет пуст<hr>'; continue;}
	$item_name = AboutCraft('#id="item_name"(.+?)</span>#is', 'item_names', $table);
	if(preg_match('/deprecated|test|тестовый|NO_NAME|Не используется/ui',$item_name)) continue;
	//if(preg_match('/test/',$item_name)) continue;
	//if(preg_match('/тестовый/ui',$item_name)) continue;
	//if(preg_match('/NO_NAME/',$item_name)) continue;
	echo $item_name.'</p>';

	if(preg_match('/Можно приобрести/',$table))
		{
			$is_trade_npc = 1;
			//echo 'Можно купить у NPC';
		}

	$grade = AboutCraft('#item_grade_(.+?)id#is', 'digits', $table);
	//echo '<p>Грейд: '.$grade.'</p>';


	$category = AboutCraft('#<td class="item-icon">(.+?)<br>#is', 'letters', $table);
	if($category == 'TESTitem') continue;
	if($category == 'TESTNPC Packs') continue;
	if($category == 'TESTBody Pack') continue;
	if($category == 'TESTitem') continue;
	if(preg_match('/deprecated|test|тестовый|NO_NAME|Не используется/ui',$category)) continue;
	//echo '<p>'.$category.'</p>';


	$description = AboutCraft('#<td colspan="4"><hr class="hr_long">(.+?)Стоимость:#is', 'descr', $table);
	$description = explode('Изготовление',$description);
	$description = $description[0];
	$description = explode('Стоимость:',$description);
	$description = $description[0];
	//echo '<p>'.$description.'</p>';


	$price_buy = AboutCraft('#Стоимость:(.+?)</tr>#is', 'digits', $table);
	echo '<p>Стоимость: '.$price_buy.'</p>';

	if(preg_match('/Цена продажи:/',$table))
	{
		$price_sale = AboutCraft('#Цена продажи:(.+?)</tr>#is', 'digits', $table);
		//echo '<p>Цена продажи: '.$price_sale.'</p>';
	}

	if(preg_match('/не нужен торговцам/',$table))
	{
		$price_sale = 0;
	}
	
	$price_type = AboutCraft('#Стоимость:(.+?)</tr>#is', 'price_type', $table);
	//echo '<p>'.$price_type.'</p>';

	$personal = 0;
	if(preg_match('/Персональный предмет/',$table))
	{
		$personal = 1;
	}
	//Закончили параметры.

	//Распыление:
	preg_match_all('#Распыляется на:(.+?)</td>#is', $table, $atom_arr);
	$atom_table = $atom_arr[1][0];
	$atom_to = AboutCraft('#item--(.+?)data#is', 'digits', $atom_table);
	//echo '<p>'.$atom_to.'</p>';
	$atoms = AboutCraft('#\((.+?)\)#is', 'digits', $atom_table);
	//echo '<p>'.$atoms.'</p>';
	if($atoms > 0 and $atom_to > 0)
	qwe("REPLACE INTO `atomization` 
	(`item_id`, `atom_to`, `atoms`)
	VALUES
	('$item_id', '$atom_to', '$atoms')");
	
	$icon = AboutCraft('#<td class="item-icon"><div style="position: relative; left: 0; top: 0;"><img src="http://archeagedatabase.net/items/(.+?)\.png#is', 'img', $table);
	
	//echo '<p>'.$icon.'</p>';
	$img = file_get_contents('http://archeagedatabase.net/items/'.$icon.'.png');
	file_put_contents('img/icons/40/'.$item_id.'.png', $img);
	//echo '<img src="'.$img.'">';
	qwe("REPLACE INTO `parsed_items` 
	(`item_id`, `price_buy`, `price_type`, `price_sale`, `is_trade_npc`, `category`, `item_name`, `description`, `personal`, `basic_grade`) 
	VALUES 
	('$item_id', '$price_buy', '$price_type', '$price_sale', '$is_trade_npc', '$category', '$item_name', '$description', '$personal', '$grade')");
	

	
	
	echo '<p>Записал</p>';
	/*
	unset($all_mat_inf);
	unset($itmarr);
	unset($arr);
	*/
}
echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
?>
