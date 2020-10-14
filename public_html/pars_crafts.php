<meta charset="utf-8">
<?php
$start = $_SERVER["REQUEST_TIME_FLOAT"];
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) exit();
?>
<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>
<?php
if(empty($_POST['go'])) exit();
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/pars_functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functs.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/functions.php';




$start_id = 8000735;
$stop_id = 8000787;
$limit = 1000;


/*
$qlast = qwe("SELECT * FROM parsed_last");
foreach($qlast as $q)
{
	$start_id = $q['item_id']+1;//На самом деле это id рецепта.
	//var_dump($start_id);
}
*/

$qlist = qwe("
SELECT * FROM `New_crafts_6.5.3` 
/*WHERE result_item_id in (SELECT item_id FROM New_items60)*/
WHERE craft_id > (SELECT item_id FROM parsed_last)
/*LIMIT 5*/
");

$icrfts = 0;
$icrfts2 = 0;
//for($rec = $start_id; $rec <= $stop_id; $rec++)
//{

foreach($qlist as $qq)
{
	$icrfts++;
	
	$new = true;
	$rec = $qq['craft_id'];
	$on_off = 1;
	
	qwe("UPDATE `parsed_last` SET `item_id` = '$rec' WHERE `id` = 1");
	/*
	//Пропускаем неиспользуемые рецепты
	$ignorq = qwe("SELECT * FROM deleted_crafts50 WHERE craft_id = '$rec'");
	if(mysqli_num_rows($ignorq)>0)
		continue;
	*/
	
	$query = qwe("SELECT `craft_id`, `on_off` FROM `crafts`
	WHERE `craft_id` = '$rec'");
	if(mysqli_num_rows($query)>0)
	{
		foreach($query as $q)
		{
			$on_off = intval($q['on_off']);
			//var_dump($on_off);			
		}
		$new = false;
		if(!$on_off)
			continue;
	}else
	{
		$new = true;
	}
	
	//if(!$new) continue;
	//if(IsCraftExistInBD($rec))
		//continue;
	
	//if(IsCraftDeletedInBD($rec))
		//continue;
	

	
	$plink = 'http://archeagecodex.com/ru/recipe/'.$rec;
	sleep(1);
	$somepage = curl($plink);
	
	//echo $somepage;
	if(!$somepage) 
	{
		//echo $rec.'error<hr>'; 
	 	continue;
	}
	preg_match_all('#<td>ID:(.+?)</table>#is', $somepage, $arr);
		$table = $arr[0][0];
	 // echo $table;
	///print_r($arr);
	//exit();

	$craft_id = AboutCraft('#ID: (.+?)td>#is', 'digits', $table);
	
	if(!($craft_id > 1))
	{
		//echo 'Рецепт пуст<hr>'; 
		continue;
	}
	//else
	//echo 'Id: '.$craft_id.' | '.'';
	?>
	Id: <a href="https://archeagecodex.com/ru/recipe/<?php echo $craft_id?>/"><?php echo $craft_id?></a>
	<?php
	echo ' | ';
	//$craft_name = AboutCraft('#<span class="item_title">(.+?)</span>#is', 'item_names', $table);
	$craft_name = $qq['rec_name'];
	echo $craft_name.' | ';


	$labor_need = AboutCraft('#Очки работы: (.+?)<br>#is', 'digits', $table);
	//echo '<p>labor_need: '.$labor_need.'</p>';


	$craft_time = AboutCraft('#Время производства: (.+?)с#is', 'craft_time', $table);
	//echo '<p>craft_time: '.$craft_time.'</p>';
	///линия
	unset($arr);
	preg_match_all('#<td>Ремесло:(.+?)<hr class="hr_long">#is', $somepage, $arr);
	$table = $arr[0][0];
	//print_r($table);
	/*
	$prof_need = AboutCraft('#Требуемый уровень ремесла: (.+?)<br>#is', 'prof_need', $table);
	echo '<p>p_need: '.$prof_need.'</p>';
	*/
	$prof_need = $qq['prof_need'];


	$profession = AboutCraft('#Ремесло: (.+?)<br>#is', 'letters', $table);
	//echo '<p>'.$profession.'</p>';
	$prof_id = profid($profession);
	//echo '<p>$prof_id'.$prof_id.'</p>';
	
	
	$dood_id = AboutCraft('#doodad--(.+?)data#is', 'digits', $table);
	//echo '<p>'.$dood_id.'</p>';

	$dood_name = AboutCraft('#Приспособление: (.+?)</a>#is', 'letters', $table);
	//echo '<p>dood_name: '.$dood_name.'</p>';
	//Закончили параметры.

	//Материалы:
	preg_match_all('#Материалы:(.+?)</table>#is', $somepage, $arr);
		$table = $arr[0][0];
	preg_match_all('#<div class="reward_counter_big">(.+?)</div>#is', $table, $arr);
	//print_r($arr[0]);
	$mats = $arr[0];
	//var_dump($mats);
	$cnt = count($mats);
	//echo '<p>Материалов: '.($cnt-1).'</p>';
	$i = 1;
	$item_name = $amount = '';
	if(preg_match('/deprecated|test|тестовый/',$item_name)) 
	{
		echo '<hr>'; continue;
	}
	$ignor_mats = [4747];
	$valid = false;
	foreach($mats as $items_ids)
	{
		preg_match_all('#item--(.+?)data#is', $items_ids, $itmarr);
		preg_match_all('#grade="(.+?)" #is', $items_ids, $gradearr);
		$item_id = DigitsOnly($itmarr[1][0]);
		$mat_grade = DigitsOnly($gradearr[1][0]);
		//var_dump($mat_grade);
		if(in_array($item_id,$ignor_mats)) break;
		$items_str = strip_tags($items_ids);
		//echo '<p>$items_str: '.$items_str.'</p>';
		
		if(preg_match('/Деньги/',$items_str))
		{
			$items_str = DigitsOnly($items_str);
			$item_name = 'Деньги';
			$amount = $items_str;
		}else
		{
			$items_str = explode(' x ',$items_str);
			$item_name = $items_str[0];
			$amount = $items_str[1];
		}
		
		$item_name = ItemNames($item_name);
		if(preg_match('/deprecated|test|тестовый/',$item_name)) break;
		
		
		if(!($amount>0)) break;
		if($i < $cnt)
		{
			//Если это материалы рецепта
			//echo '<p>'.$item_name.': '.$amount.'</p>';
			$all_mat_inf[] = $item_id.'_delimiter_'.$item_name.'_delimiter_'.$amount.'_delimiter_'.$mat_grade;
		}
		else
		{
			//Если это результат рецепта
			$valid = true;
			//echo 'Результат: '.$item_name.': '.$amount.'<br>';
			
			if($new)
			{
				qwe("REPLACE INTO `crafts` 
				(`craft_id`, 
				`rec_name`, 
				`dood_id`, 
				`dood_name`, 
				`result_item_id`, 
				`result_item_name`, 
				`labor_need`, 
				`profession`,
				`prof_id`,
				`prof_need`, 
				`result_amount`, 
				`craft_time`,
				`grade`)
				VALUES 
				('$craft_id', 
				'$craft_name', 
				'$dood_id', 
				'$dood_name', 
				'$item_id', 
				'$item_name', 
				'$labor_need', 
				'$profession',
				'$prof_id',
				'$prof_need', 
				'$amount', 
				'$craft_time',
				'$mat_grade')
				");
				echo 'добавлен';
			}else
			{
				qwe("
				UPDATE `crafts` SET
				`rec_name`= '$craft_name', 
				`dood_id` = '$dood_id', 
				`dood_name` = '$dood_name', 
				`result_item_id` = '$item_id', 
				`result_item_name` = '$item_name', 
				`labor_need` = '$labor_need', 
				`prof_need` = '$prof_need', 
				`result_amount` = '$amount', 
				`craft_time` = '$craft_time',
				`grade` = '$mat_grade'
				WHERE `craft_id` = '$craft_id'
				");
				echo 'обновлен';
			}
			
			
			
		}

		$i++;
	}
	
	if($valid)
	{	
		qwe("DELETE FROM `craft_materials` WHERE `craft_id` = '$craft_id'");
		foreach($all_mat_inf as $ami)
		{
			//echo $ami.'<br>';
			$mat_inf = explode('_delimiter_',$ami);
			$mat_id = $mat_inf[0];
			$mat_name = $mat_inf[1];
			$mater_need = $mat_inf[2];
			$mat_grade = intval($mat_inf[3]);
			$result = qwe("
			REPLACE INTO `craft_materials` 
				(`craft_id`, `item_id`, `result_item_id`, `mater_need`, `item_name`, `result_item_name`,`mat_grade`)
				VALUES 
				('$craft_id', '$mat_id', '$item_id', '$mater_need', '$mat_name', '$item_name', '$mat_grade')
				");
			//var_dump($result);			
		}	
	}
	else
		echo 'Рецепт не нужен';
	
	unset($all_mat_inf);
	unset($itmarr);
	unset($arr);
	echo '<hr>';

    $icrfts2++;
    if($icrfts2 > $limit) break;
}
echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';

function profid($profession)
{
	$qwe = qwe("SELECT * FROM profs
	WHERE profession = '$profession'
	");
	if(!$qwe or $qwe->num_rows == 0)
		return 25;
	$qwe = mysqli_fetch_assoc($qwe);
	$prof_id = intval($qwe['prof_id']);
	if($prof_id) return $prof_id;
	
	return 26;
}
?>
