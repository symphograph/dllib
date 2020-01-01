<?php
function PumpRescost($rv, $orcost, $repprice, $forlost, $price_q, $user_id, $dzprice, $honorprice, $prof_q, $soverprice, $bad_crafts)
{
	global $total, $itog, $craft_id, $rec_name, $item_id, $lost, $forlostnames, $bad_crafts;
		$lvlranges = array('0', '0', '5', '10', '15', '20', '20', '20', '20', '25', '30', '40'); $lvl = 0;
		$craft_id = $rv;
		$qitog = qwe("SELECT `result_item_name`, `result_item_id`, `result_amount`, `dood_name`, `labor_need`, `prof_need`, `profession`, `rec_name` 
		FROM `crafts` 
		WHERE `craft_id` = '$craft_id' 
		ORDER BY `result_item_id`") 
		or die("ERROR: ".mysql_error());
	 $arritog = mysqli_fetch_assoc($qitog);
 		//if ($arritog['result_amount']!== 0 and ctype_digit($arritog['result_amount']))
		$itog = $arritog['result_amount'];
		$or = $arritog['labor_need'];
        $res_name = $arritog['result_item_name'];
		$item_id = $arritog['result_item_id'];
		$rec_name = $arritog['rec_name'];
        $profession = $arritog['profession'];
		$econom_or = 0;
			foreach($prof_q as $profk)
				{
					if ($profk['prof'] == $profession) 
						{$lvl = $profk['lvl'];
						$econom_or = $lvlranges[$lvl];
						break;
						}
				}
		if($or > 0)
		$or = ceil($or*(100-$econom_or)/100); else $or = 0;
		$dood = $arritog['dood_name'];
		//$next = $arritog['next'];
		if($arritog['dood_name'] == 'Лаборатория')
			$itog = $itog*1.1;
	
	$group_cr_q = qwe("SELECT `item_name`, `amount`, sum(`amount`) as `sum` FROM `craft_groups` WHERE `group_id` = 
(SELECT `group_id` FROM `craft_groups` WHERE `craft_id` = '$craft_id')");
	
			  foreach($group_cr_q as $gcr)
			  {
				  $am_sum = $gcr['sum']; 
			  }
	if($am_sum > 0)
		{$cr_part = $itog/$am_sum;
	$itog = $itog/$cr_part;
		$group_cr_q = qwe("SELECT * FROM `craft_materials` WHERE `craft_id` = '$craft_id' AND `mater_need` > 0");
		 $sum = 0; $price = 0;
			 foreach($group_cr_q as $gcr)
			 {
				$mater = $gcr['item_id'];
				$mater_need = $gcr['mater_need']; 
			 
		 $q_craft_pr = qwe("SELECT * FROM `user_crafts` where  `isbest` > 0 and `item_id` = '$mater' and `user_id` ='$user_id'");
		$arrbest = mysqli_fetch_assoc($q_craft_pr);
		if($arrbest['isbest'] == 3) $price = $arrbest['auc_price'];
					 if($price == 0)
					{
							if(isset($forlost[$mater]))
							{$price = $forlost[$mater];} //echo '<p>откопал в промежуточных: '.$mater_name.' '.$price.'</p>';
					else 
						{if($key['craftable'] <1 and $personal <1) $lost[] = $mater;}
						
					}
		 $matsum = $mater_need*$price;
		$sum = $sum+$matsum;
				 
		    }
		 $total = $sum+$or*$orcost;
		 return;
		}
	
	//Запрашиваем про материалы
$query2 = qwe(
"SELECT `items`.`craftable`, `craft_materials`.`mater_need`, `items`.`item_name`, `items`.`price_buy`, `items`.`price_sale`, `items`.`price_type`, `items`.`is_trade_npc`, `items`.`item_id`, `items`.`personal` 
FROM `craft_materials`, `items`
 WHERE `items`.`ismat` = 1 and `craft_materials`.`craft_id` = '$craft_id' 
 AND `items`.`item_id` = `craft_materials`.`item_id` and `items`.`on_off` = '1' 
 ORDER BY `item_id`");
$sum = 0;
foreach($query2 as $key)
	{
		$price = 0;
		$mater = $key['item_id'];
		$mater_need = $key['mater_need'];
		$foundprice = false;
		if($mater_need < 0)
		{
			foreach($price_q as $pkey)
			{
			 if($pkey['user_id'] == $user_id and $pkey['item_id'] == $mater)
			  {$price = $pkey['auc_price'];break;}
				if($pkey['item_id'] == $mater and !$foundprice)
				 {$foundprice = $pkey['auc_price'];
				 $price = $foundprice;
				 }
			}
			$matsum = $mater_need*$price;
		    $sum = $sum+$matsum;
			continue;
		}
		
		$mater_name = $key['item_name'];
		$personal = $key['personal'];
		
		//Проверим нет ли юзерской цены на нпс-предмет.
		if($personal != 1 and $key['is_trade_npc'] > 0)
		{
			foreach($price_q as $pkey)
			{
			 if($pkey['user_id'] == $user_id and $pkey['item_id'] == $mater)
			  {$price = $pkey['auc_price'];break;}
				
			}
		}
		
		
		
		//echo $mater_name.'<br>';
		$forlostnames[$mater_name] = $mater;
		 
		if(($key['is_trade_npc'] > 0 and $price == 0) or $mater == 500)
		{	
			if(($key['price_type'] == 'gold') or $mater == 500 or $personal == 1)
				$price = $key['price_buy'];
			if($key['price_type'] == 'Ремесленная репутация')
			$price = $key['price_buy']*$repprice;
			if($key['price_type'] == 'Честь')
			$price = $key['price_buy']*$honorprice;
			if($key['price_type'] == 'Дельфийская звезда')
			$price = $key['price_buy']*$dzprice;
			if($key['price_type'] == 'Золотой соверен')
			$price = $key['price_buy']*$soverprice;
		}

		if($price == 0 and ($key['craftable'] <1 or $mater_need <0))
		{
			$foundprice = false;
			 foreach($price_q as $pkey)
			{
			 if($pkey['user_id'] == $user_id and $pkey['item_id'] == $mater)
			  {$price = $pkey['auc_price'];break;}
			 if($pkey['item_id'] == $mater and !$foundprice)
				 {$foundprice = $pkey['auc_price'];
				 $price = $foundprice;
				 }
			}
		}

		if($price == 0 and $mater_need >0){ 
		$q_craft_pr = qwe("SELECT * FROM `user_crafts` where  `isbest` > 0 and `item_id` = '$mater' and `user_id` ='$user_id'");
		$arrbest = mysqli_fetch_assoc($q_craft_pr);
		if($arrbest['isbest'] == 3) $price = $arrbest['auc_price'];
		}
		if($price == 0 and $mater_need >0)
		{
				if(isset($forlost[$mater]))
			{ 
				$price = $forlost[$mater]; //echo '<p>откопал в промежуточных: '.$mater_name.' '.$price.'</p>';
			}
		else 
			{
				if($key['craftable'] <1 and $personal <1) $lost[] = $mater;
			}
		}

		$matsum = $mater_need*$price;
		$sum = $sum+$matsum;
 }
	
	
	$total = $sum+$or*$orcost;
};
?>