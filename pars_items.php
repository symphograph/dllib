<meta charset="utf-8">
<?php
$start = microtime(true);

require_once 'includs/ip.php';
if(!$myip) exit();
$start_id = $_POST['start_id'] ?? 0;
$start_id = intval($start_id);
$stop_id = $_POST['stop_id'] ?? $start_id;
$stop_id = intval($stop_id);
if(!$stop_id) 
	$stop_id = $start_id;
//var_dump($stop_id);
?>
<!--Проверь  $start_id и $stop_id-->
<form method="post" action="">
<input type="number" placeholder="start_id" value="<?php echo $start_id?>" name="start_id"/>
<?php /*?><input type="number" placeholder="stop_id" value="<?php echo $stop_id?>" name="stop_id"/><?php */?>
<input type="submit" name="go" value="go">	
</form>
<?php
if(empty($_POST['go'])) exit();
include_once 'includs/config.php';
include_once 'functions/pars_functs.php';
include_once 'functions/filefuncts.php';
include_once 'functions/functs.php';
include_once 'functions/functions.php';

$start_id = $_POST['start_id'] ?? 0;
$start_id = intval($start_id);
$stop_id = $_POST['stop_id'] ?? $start_id;
$stop_id = intval($stop_id);
//if(!$start_id) die;
//if(!$stop_id) die;
//var_dump($stop_id);
//$stop_id = 20408;
//$itm = 98;
/*	   
$qwe = qwe("
SELECT `result_item_id` as `item_id` 
FROM `craft_materials`
WHERE `result_item_id` NOT IN
(SELECT `item_id` FROM `items`)
GROUP BY `result_item_id`
LIMIT 10
");*/
$qwe = qwe("
SELECT `item_id` FROM `New_items61`
");
$i = 0;
$ver = random_str(8);
//for($itm = $start_id; $itm <= $stop_id; $itm++)
//{
foreach($qwe as $q)
{
 
	$itm = $q['item_id'];
 	$new = 1;
 	$new = (!IsItemExistInBD($itm));
 	if(!$new) continue;
	$i++;
 	if($i>100) break;
	qwe("UPDATE `parsed_last` SET `item_id` = '$itm' WHERE `id` = 1");
	
	echo '<hr>';
	$plink = 'http://archeagecodex.com/ru/item/'.$itm;
 //echo $itm;
	sleep(1);
	$somepage = curl($plink);
	

	$is_trade_npc = 0;
	
	if(!$somepage) echo 'error';
 //var_dump($somepage);
	preg_match_all('#<td colspan="2">ID:(.+?)<div class="addon_info">#is', $somepage, $arr);
	
	if(!$arr[0][0]) {echo 'Предмет пуст<hr>'; continue;}
	else
	$table = $arr[0][0];
	$price_type = '';
	
	$item_id = AboutCraft('#ID: (.+?)td>#is', 'digits', $table);
	$item_id = intval($item_id);
	echo 'Id: '.$itm.' | ';

	if(!$item_id) {echo 'Предмет пуст<hr>'; continue;}
	$item_name = AboutCraft('#id="item_name"(.+?)</span>#is', 'item_names', $table);
	if(preg_match('/deprecated|test|Тест: |тестовый|NO_NAME|Не используется/ui',$item_name)) continue;
	
	
		
	
	
	
	echo $item_name.'<br>';
	//if(!$new) continue;
	//exit();


	$grade = AboutCraft('#item_grade_(.+?)id#is', 'digits', $table);
	//echo '<p>Грейд: '.$grade.'</p>';


	$category = AboutCraft('#<td class="item-icon">(.+?)<br>#is', 'letters', $table);
 	if(array_search($category,['TESTitem','TESTNPC Packs','TESTBody Pack','TESTitem']))
		continue;
 
 	$categ_id = 1;
 	echo $category.'<br>';
 	$querycat = qwe("SELECT `id` FROM `item_categories` WHERE `name` = '$category'");
 	foreach($querycat as $qc)
	{
		$categ_id = $qc['id'];
	}
 
 	

 	
	
	if(preg_match('/deprecated|test|тестовый|NO_NAME|Не используется/ui',$category)) continue;
	//echo '<p>'.$category.'</p>';


	$description = AboutCraft('#<td colspan="4"><hr class="hr_long">(.+?)Стоимость:#is', 'descr', $table);
	$description = explode('Изготовление',$description);
	$description = $description[0];
	$description = explode('Стоимость:',$description);
	$description = $description[0];
 	$description = str_replace('Ячейки для гравировки:<br>','',$description);
	//echo '<p>'.$description.'</p>';


	$price_buy = AboutCraft('#Стоимость:(.+?)</tr>#is', 'digits', $table);
	//echo '<p>Стоимость: '.$price_buy.'</p>';

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
 	$valut_id = ValutID($price_type);
	
	if(preg_match('/Можно приобрести/',$table) or ($valut_id and $valut_id !=500))
	{
		$is_trade_npc = 1;
		//echo 'Можно купить у NPC';
	}

	$personal = 0;
	if(preg_match('/Персональный предмет/',$table))
	{
		$personal = 1;
	}
	//Закончили параметры.

	//Распыление:
	$atom = preg_match_all('#Распыляется на:(.+?)</td>#is', $table, $atom_arr);
 	if($atom)
	{
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
	}
 
	
 	
 	
 	if($new)
	qwe("REPLACE INTO `items` 
	(`item_id`, `price_buy`, `price_type`, `valut_id`, `price_sale`, `is_trade_npc`, `category`, `categ_id` , `item_name`, `description`, `personal`, `basic_grade`) 
	VALUES 
	('$item_id', '$price_buy', '$price_type', '$valut_id', '$price_sale', '$is_trade_npc', '$category', '$categ_id', '$item_name', '$description', '$personal', '$grade')");
	
 	/*else
 	qwe("UPDATE `items` SET 
	`price_buy` = '$price_buy', 
	`price_type` = '$price_type', 
	`price_sale` = '$price_sale', 
	`is_trade_npc` = '$is_trade_npc', 
	`item_name` = '$item_name', 
	`description` = '$description', 
	`personal` = '$personal', 
	`basic_grade` = '$grade'
	WHERE `item_id` = '$item_id'
	");*/
	
	$iconfile = ParsIcons($item_id);
	if($iconfile)
	{
		
		?><div style="width: 40px; height: 40px; background-image: url(<?php echo 'img/icons/50/'.$iconfile.'.png?ver='.$ver?>)"></div><?php
	}
	
	//echo '<p>Записал</p>';
	/*
	unset($all_mat_inf);
	unset($itmarr);
	unset($arr);
	*/
}
echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';


?>
