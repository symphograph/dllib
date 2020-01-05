<?php

$craftsq = qwe("SELECT * FROM `craft_materials` WHERE `result_item_id` = '$item_id'");
$count_crafts = mysqli_num_rows($craftsq);
//echo 'Надо посчитать:'.$count_crafts.' итемов';
$x = 1;
$icrft = 0;
$crafta = 0;
$crafts = array();
$crdeep = array();
$deep = 0;
$forlost = array(); 
$crftorder[] = $item_id;
	///Выясняем всё что нужно для дерева рецептов.
	PumpRes($item_id, $craftsq, $dbLink, $x, $crafta, $icrft, $crdeep, $crftorder, $user_id);
   ///Массив необходимого получен. Запрашиваем рецепты, которые ещё не считали:
	$selcrafts = "SELECT * from `crafts` WHERE `on_off` = 1  and `result_item_id` in (".(implode(', ', $crdeep)).") ORDER BY `deep` DESC, `result_item_id`";
	$querycrafts = qwe($selcrafts);
	//echo implode(', ', $crdeep); exit();
	$userbest =array();
    $b = mysqli_num_rows($querycrafts);
	$selucrafts = "SELECT * FROM `user_crafts` WHERE `user_id` = '$user_id' and `isbest` > 1";
	$quucrafts = qwe($selucrafts) or die("Invalid query: " . mysql_error());;

	foreach($quucrafts as $ucr){
		$userbest[$ucr['craft_id']] = $ucr['isbest'] ;
		$userbestaucprice[$ucr['craft_id']] =  $ucr['auc_price'];
		//echo $ucr['craft_id'];
	}
	//print_r($userbest); exit();

//Если есть непосчитанные рецепты, считаем
if($b > 0)
{
	
	PumpOrrepcost($user_id, $server_group);
	
	foreach($querycrafts as $key){	
	$craftkeys1[$key['result_item_id']][] = $key['craft_id'];
	}
	///Запускаем расчет крафта выбранных предметов.
		
	foreach($craftkeys1 as $key => $v)
	{	
		//echo '<br>'.$key.' имеет '.count($v).' рецептов: '.implode(",", $v).'<br>';
		//exit();
		$user_choose = 0; $best_type = 0;
		if (count($v)>1)
		{$r = 0;
				 for ($i = 1; $i <= count($v); $i++)
				 {

						 $rv = $v[$r]; 	
						 if(array_key_exists($rv,$userbest) and $best_type < 2)
					   {$best_type = $userbest[$rv];
					   $user_choose = $rv;
					   }

						PumpRescost($rv, $orcost, $repprice, $forlost, $price_q, $user_id, $dzprice, $honorprice, $prof_q, $soverprice); $r++;



						$mycost= $total/$itog;
						$krytery = $mycost/$itog;
						if ($mycost != 0){
						$krytery2[$craft_id] = $mycost/$itog;
						$mycost= round($mycost,0);
						$mycost2[$craft_id] = $mycost;
						};

						$itemarr[] = $item_id;
						$craftarr[] = $craft_id;
						$mycostarr[] = $mycost;
						$bestcraft[] = 0;
						$auccraft[] = 0;
			   }
		   if($best_type < 2)
		$recomend_craft = array_search(min($krytery2),$krytery2);
		if($best_type > 1) $recomend_craft = $user_choose;
		$recomend_mycost = $mycost2[$recomend_craft];

		//echo '<p>Рекомендуемый рецепт: '.$recomend_craft.'</p>';
		//echo '<p>Стоимость предмета по этому рецепту: '.$recomend_mycost.'</p>';
		/*$baza[] = array('item_id' => $item_id, 'rec_name' => $rec_name, 'mycost' => $mycost,'craft_id' => $recomend_craft);*/
		 $itemarr[] = $item_id;
		$craftarr[] = $recomend_craft;
		$mycostarr[] = $recomend_mycost;
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
		$krytery2= array();
		$mycost2 = array();
		}
		else
		{ if (count($v)>0){ $user_choose = 0; $best_type = 0;
			$r = 0;
				 $rv = $v[$r];
				  if(array_key_exists($rv,$userbest) and $best_type < 2)
			   {$best_type = $userbest[$rv];
			   $user_choose = $rv;
			   }
		PumpRescost($rv, $orcost, $repprice, $forlost, $price_q, $user_id, $dzprice, $honorprice, $prof_q, $soverprice, $bad_crafts);

		$mycost= round($total/$itog,0);
		/*$baza[] = array('item_id' => $item_id, 'rec_name' => $rec_name, 'mycost' => $mycost,'craft_id' => $craft_id);*/

		$itemarr[] = $item_id;
		$craftarr[] = $craft_id;
		$mycostarr[] = $mycost;
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

		if(count($lost)>0)
		{
			$lost = array_unique($lost);
			echo '<p><b>Не могу рассчитать полную стоимость.</b><br>Сообщите, пожалуйста, цены следующих предметов:<p>';
			$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
			foreach($lost as $vl)
			{
				$lostname = array_search($vl, $forlostnames);
				if($vl == 1) $lostname = 'Жетон на 150 кристаллов';
				if($vl == 3) $lostname = 'Ремесленная репутация';
				$item_link= str_replace(" ","+",$lostname);
				echo $hrefself.$item_link.'&query_id='.$vl.'" text-decoration: none; style="color: #6C3F00;" target="_blank">'.$lostname.'</a><br>';
				$lost2[] = $vl;
			}

			//include_once 'pageb/footer.html';
			//exit();
		}

	for ($i=0; $i < count($craftarr); $i++) 
	{
		if(in_array($craftarr[$i],$lost)) continue;

		if($itemarr[$i] > 0)
		{
			///Пишем в базу чего посчитали.
			$sql="INSERT INTO `user_crafts` (
			`item_id` , `user_id`, `isbest`, `craft_id`, `craft_price`, `auc_price` ,`updated`) 
			VALUES ('".$itemarr[$i]."', '".$user_id."', '".$bestcraft[$i]."', '".$craftarr[$i]."', '".$mycostarr[$i]."', '".$auccraft[$i]."' ,now()) ON DUPLICATE KEY UPDATE `isbest` = '".$bestcraft[$i]."', `craft_price` = '".$mycostarr[$i]."', `auc_price` = '".$auccraft[$i]."',`updated` = now()";

			qwe($sql) or die(mysqli_error ($dbLink));

		 } 
	}; 
};

?>