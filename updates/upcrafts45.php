<?php
require_once '../includs/ip.php';
if(!$myip) exit();
include '../includs/config.php';
include '../functions/functions.php';
include '../functions/pars_functs.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Рецепты 4.5</title>
</head>

<body>
<?php
$query = qwe("
SELECT * FROM `parsed_crafts` 
WHERE `on_off` = 1
AND `result_amount` > 0
/*and `craft_id` = 10326*/
");
foreach($query as $q)
{
	//printr($q);
	$rec_name = $q['rec_name'];
	$dood_id = intval($q['dood_id']);
	$dood_name = $q['dood_name'];
	$result_item_name = $q['result_item_name'];
	$labor_need = intval($q['labor_need']);
	$profession = $q['profession'];
	$prof_need = intval($q['prof_need']);
	$result_amount = intval($q['result_amount']);
	$dood_group = $q['dood_group'];
	$craft_time = intval($q['craft_time']);
	$craft_id = intval($q['craft_id']);
	$result_item_id = intval($q['result_item_id']);
	$rec_data = $q['rec_data'];
	$mater_ids = $q['mater_ids'];
	$craft_time = intval($q['craft_time']);
	
	
	
	$mater_ids = explode(',',$mater_ids);
	$mater_ids = array_unique($mater_ids);
	$mater_ids = array_diff($mater_ids,[$result_item_id]);
	//printr($mater_ids);
	
	$rec_data = explode('   ',$rec_data);
	$rec_data = array_diff($rec_data,['']);
	if(count($rec_data) != count($mater_ids))
	{
		//echo '<a href="http://archeagecodex.com/ru/recipe/'.$craft_id.'">'.$craft_id.'</a><br>';
		//qwe("UPDATE `parsed_crafts` SET `on_off` = 1 WHERE `craft_id` = '$craft_id'");
		//qwe("UPDATE `crafts` SET `on_off` = 1 WHERE `craft_id` = '$craft_id'");
		echo $craft_id.' не соовпали материалы<br>';
	continue;
	}
	$sliv = array_combine($mater_ids,$rec_data);
	qwe("DELETE FROM `craft_materials` WHERE `craft_id` = '$craft_id'");
	foreach($sliv as $mater_id => $v)
	{
		
		if(preg_match('/ x /',$v))
		{
			$varr = explode(' x ',$v);
			$need = DigitsOnly($varr[1]);
			//echo $mater_id.' '.$varr[0].' '.$need.'<br>';
			
			//continue;
		}
		if(preg_match('/Деньги/',$v))
		{
			$need = DigitsOnly($v);
			//echo 'медяки: '. $need.'<br>';
		}
		
		qwe("INSERT INTO `craft_materials` 
			(`craft_id`,`item_id`,`result_item_id`,`mater_need`,`item_name`,`result_item_name`)
			VALUES
			($craft_id,$mater_id,$result_item_id,$need,
			(SELECT `item_name` FROM `items` WHERE `item_id` = '$mater_id'),
			(SELECT `item_name` FROM `items` WHERE `item_id` = '$result_item_id')
			)
			");	
			
	}///Закончили с материалами
	
	//Новые рецепты вставляются
	//if($result_item_id == 45023)
		//echo 'Нашел';
	$insertq = qwe("INSERT INTO `crafts` 
		(`craft_id`, 
		`rec_name`, 
		`dood_id`, 
		`dood_name`, 
		`result_item_id`, 
		`result_item_name`, 
		`labor_need`, 
		/*`profession`, */
		`prof_need`, 
		`result_amount`, 
		`my_craft`, 
		`on_off`,
		`craft_time`)
		VALUES 
		('$craft_id', 
		'$rec_name', 
		'$dood_id', 
		'$dood_name', 
		'$result_item_id', 
		'$result_item_name', 
		'$labor_need', 
		/*(SELECT `profession` FROM `crafts_40` WHERE `craft_id` = '$craft_id'), */
		'$prof_need', 
		'$result_amount', 
		'0', 
		'1',
		'$craft_time')
		");
	if(!$insertq) echo '<p>'.$result_item_id.' Не получилось записать</p>';
		//or die("<p>не смог в базу</p>" . mysqli_error($dbLink));
qwe("UPDATE `crafts` SET `result_amount` = '$result_amount' WHERE `craft_id` = '$craft_id'");
}
qwe("UPDATE `items` SET is_trade_npc = 1 WHERE price_type = 'Честь'");

?>
</body>
</html>