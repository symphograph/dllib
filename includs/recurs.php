<?php

$craftsq = qwe("
SELECT * 
FROM `craft_materials` 
WHERE `result_item_id` = '$item_id'
");
$count_mats = mysqli_num_rows($craftsq);
//echo '<p>Надо посчитать:'.$count_mats.' итемов</p>';
$x = 1;
$icrft = 0;
$crafta = 0;
$crafts = array();
$crdeep = array();
if(!isset($lost))
	$lost = [];
$deep = 0;
$forlost = array(); 
$crftorder[] = $item_id;
	///Выясняем всё что нужно для дерева рецептов.
	res($item_id, $craftsq, $x, $crafta, $icrft, $crdeep, $crftorder);

   ///Массив необходимого получен. Запрашиваем рецепты, которые ещё не считали:
	$selcrafts = "
	SELECT * from `crafts` 
	WHERE `on_off` = 1  
	AND 
		`result_item_id` IN (".(implode(', ', $crdeep)).") 
	ORDER BY 
		`deep` DESC, `result_item_id`";
	$querycrafts = qwe($selcrafts);
	//echo implode(', ', $crdeep); exit();
	$userbest =array();
    $b = mysqli_num_rows($querycrafts);
	$selucrafts = "SELECT * FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` > 1";
	$quucrafts = qwe($selucrafts);

	foreach($quucrafts as $ucr)
	{
		$userbest[$ucr['craft_id']] = $ucr['isbest'] ;
		$userbestaucprice[$ucr['craft_id']] =  $ucr['auc_price'];
		//echo $ucr['craft_id'];
	}
	//print_r($userbest); exit();

//Если есть непосчитанные рецепты, считаем
if($b > 0)
{
	
	//orrepcost($user_id, $server_group);
	if(!isset($orcost))
	$orcost = PriceMode(2,$user_id)['auc_price'];
	
	foreach($querycrafts as $key){	
	$craftkeys1[$key['result_item_id']][] = $key['craft_id'];
	}
	//printr($craftkeys1);
	
	$craftarr = CraftsBuffering($craftkeys1);
	///Запускаем расчет крафта выбранных предметов.
	//var_dump($craftkeys1);
/*
	foreach($craftkeys1 as $key => $v)
	{	
		$krytery2 = array();
		//echo '<br>'.$key.' имеет '.count($v).' рецептов: '.implode(",", $v).'<br>';
		//exit();
		$user_choose = 0; $best_type = 0;
		//Если рецептов несколько
		if (count($v)>1)
		{$r = 0;
		 
		 	
			 for ($i = 1; $i <= count($v); $i++)
			 {

					$rv = $v[$r]; 	
					if(array_key_exists($rv,$userbest) and $best_type < 2)
				   {$best_type = $userbest[$rv];
				   $user_choose = $rv;
				   }
				

					$mycost = rescost($rv, $forlost)[0]; $r++;
					//var_dump($item_id);
					
					//echo $itog.' за '.$total.'<br>';
					//$mycost= round($total/$itog,0);
				 
				 	//var_dump($mycost);
					$krytery = $mycost;
					if ($mycost != 0)
					{
						$krytery2[intval($craft_id)] = $mycost;
						$mycost= round($mycost,0);
						$mycost2[$craft_id] = $mycost;
					};

					$itemarr[] = $item_id;
					$craftarr[] = $craft_id;
					$mycostarr[] = $mycost;
				 	//$or_arr[]= $total_or;
					$bestcraft[] = 0;
					$auccraft[] = 0;
			 }
			if($best_type < 2)
			{
				
					$min = min($krytery2);
				//echo $min;
					$recomend_craft = array_search($min,$krytery2);
				//echo '$best_type < 2 '.$recomend_craft;
				//var_dump($krytery2);
			}
			if($best_type > 1) $recomend_craft = $user_choose;
			$recomend_mycost = $mycost2[$recomend_craft];
			//var_dump($recomend_mycost);

			//echo '<p>Рекомендуемый рецепт: '.$recomend_craft.'</p>';
			//echo '<p>Стоимость предмета по этому рецепту: '.$recomend_mycost.'</p>';
		
			$itemarr[] = $item_id;
			$craftarr[] = $recomend_craft;
			$mycostarr[] = $recomend_mycost;
		 	//$or_arr[]= $total_or;
			if($best_type < 2)
			$bestcraft[] = 1; else $bestcraft[] = $best_type;
			if($best_type < 3)
			$forlost[$item_id] = $recomend_mycost;
			if($best_type == 3)
			$forlost[$item_id] = $userbestaucprice[$recomend_craft];
			if($best_type == 3)
			$auccraft[] = $userbestaucprice[$recomend_craft];
			else $auccraft[] = 0;
		//echo '<p>'.$item_id.' '.$recomend_mycost.'</p>';
		}
		else
		//Если рецепт только один
		{ 
			if (count($v)>0)
			{ 
				$user_choose = 0; $best_type = 0;
				$r = 0;
				$rv = $v[$r];
				
				if(array_key_exists($rv,$userbest) and $best_type < 2)
				{
					$best_type = $userbest[$rv];
					$user_choose = $rv;
				}
				
				$mycost = rescost($rv, $forlost)[0];
				//var_dump($total);
				//$mycost= round($total/$itog,0);
				

				$itemarr[] = $item_id;
				$craftarr[] = $craft_id;
				$mycostarr[] = $mycost;
				//$or_arr[]= $total_or;
				if($best_type < 2)
				$bestcraft[] = 1; else $bestcraft[] = $best_type;
				if($best_type < 3)
				  $forlost[$item_id] = $mycost;
				 if($best_type == 3)
				$forlost[$item_id] = $userbestaucprice[$craft_id];
				if($best_type == 3)
				 $auccraft[] = $userbestaucprice[$craft_id];
				 else $auccraft[] = 0;
				//echo '<p>'.$item_id.' '.$mycost.'</p>';
		   }
		 };
	}; 
*/		
		if(!in_array($_SERVER['SCRIPT_NAME'],['/hendlers/packs_list.php']))
		{
			if(count($lost)>0)
			{
				MissedList($lost);
				exit();
			}
		}
		

	$i = 0;
/*
	for ($i=0; $i < count($craftarr); $i++)
	{
		
			$mycst = $mycostarr[$i];
		//echo '$craftarr: '.$craftarr[$i].' | '.$mycst.'<br>';
			
		if($itemarr[$i] > 0 and count($lost)==0)
		{
			///Пишем в базу чего посчитали.
			//echo '<p>'.$i.'</p>';
			$sql="REPLACE INTO `user_crafts` (
			`item_id` , `user_id`, `isbest`, `craft_id`, `craft_price`, `auc_price` ,`updated`) 
			VALUES ('".$itemarr[$i]."', 
			'".$user_id."', 
			'".$bestcraft[$i]."', 
			'".$craftarr[$i]."', 
			'$mycst', 
			'".$auccraft[$i]."' 
			,now())";

			//qwe($sql);
			//echo 'cr: '.$craftarr[$i].' | it: '.$itemarr[$i].' | b: '.$bestcraft[$i].'<br>';
			
			if(in_array($bestcraft[$i],[1,2]))
			{
				$best_itarr[] = $craftarr[$i];
				//echo $best_itarr[$i].'<br>';
			}
		 }
		
	}
*/
}


//printr($craftarr);
//$craftarr = [9257];
//echo $_SERVER['SCRIPT_NAME'];
//$NeedOrScripts = ['/catalog.php','/hendlers/item.php'];
//if(in_array($_SERVER['SCRIPT_NAME'],$NeedOrScripts))//Фул ОР считаем только для крафкулятора.
	AllOrRecurs($craftarr,$user_id);

//if(in_array($_SERVER['SCRIPT_NAME'],$NeedOrScripts))

?>