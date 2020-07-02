<?php
function CraftsObhod($item_id,$dbLink,$user_id,$server_group,$server,$prof_q)
{
	global $total, $itog, $craft_id, $rec_name, $item_id, $lost, $forlostnames, $orcost, $mat_deep, 
		$crafts, $crdeep, $deeptmp, $craftsq, $icrft,$crftorder;
	include $_SERVER['DOCUMENT_ROOT'].'/includs/recurs.php';
	//echo $item_id.'<br>';
}
function rescost($rv, $forlost)
{
	global $craft_id, $rec_name, $item_id, $lost, $forlostnames, $trash, $user_id, $orcost;
	if(!isset($orcost))
	$orcost = PriceMode(2,$user_id)['auc_price'] ?? false;
	 $lvl = 0;
		$craft_id = $rv;
	//echo '<p>'.$craft_id.'</p>';
		$qitog = qwe("SELECT
	`crafts`.`craft_id`,
	`crafts`.`dood_id`,
	`crafts`.`dood_name`,
	`crafts`.`result_item_id`,
	`crafts`.`result_item_name`,
	`crafts`.`labor_need`,
	round(`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100,0) AS `labor_need2`,
	`crafts`.`result_amount`,
	`crafts`.`craft_time`,
	`crafts`.`prof_id`,
	`crafts`.`rec_name`,
	`user_profs`.`lvl`,
	`prof_lvls`.`min`,
	`prof_lvls`.`max`,
	`prof_lvls`.`save_or`,
	`prof_lvls`.`save_time`,
	`profs`.`profession`,
	`profs`.`used`
	FROM
		`crafts`
	LEFT JOIN `user_profs` ON `user_profs`.`prof_id` = `crafts`.`prof_id`
	AND `crafts`.`on_off` = 1
	AND `user_profs`.`user_id` = '$user_id'
	AND `crafts`.`prof_id`= `user_profs`.`prof_id`
	LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
	LEFT JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
	WHERE `crafts`.`craft_id` = '$craft_id'");

	$arritog = mysqli_fetch_assoc($qitog);
	$itog = $arritog['result_amount'];
	$or = $arritog['labor_need'];
	$res_name = $arritog['result_item_name'];
	$item_id = $arritog['result_item_id'];
	$rec_name = $arritog['rec_name'];
	$profession = $arritog['profession'];
	$prof_id = $arritog['prof_id'];
	$used = $arritog['used'];
	
	if($used > 0)
		$or = $arritog['labor_need2'];
	
	$total_or = $or;
	$dood = $arritog['dood_name'];
	
	if($arritog['dood_name'] == 'Лаборатория')
		$itog = $itog*1.1;
	
	$groupcraft = GroupCraft($arritog,$or);
	if($groupcraft)
	{
		$total = $groupcraft;
		return [round($total/$itog),0];
	}
		  
	
	
	//Запрашиваем про материалы
	$query2 = qwe(
	"SELECT 
	`items`.`craftable`, 
	`craft_materials`.`mater_need`,
	`items`.`item_name`, 
	`items`.`price_buy`, 
	`items`.`price_sale`, 
	`items`.`price_type`, 
	`items`.`is_trade_npc`, 
	`items`.`item_id`, 
	`items`.`forup_grade`,
	`items`.`categ_id`,
	`items`.`personal`,
	`user_crafts`.`isbest`,
	`user_crafts`.`craft_price`,
	craft_buffer2.spm*craft_materials.mater_need as spm2,
	craft_buffer2.craft_price as buffer_price
	FROM 
	`craft_materials`
	INNER JOIN `items`
	ON `items`.`item_id` = `craft_materials`.`item_id`
	AND `craft_materials`.`craft_id` = '$craft_id'
	AND `items`.`ismat` = 1 
	AND `items`.`on_off` = '1'
	LEFT JOIN `user_crafts` 
	ON `items`.`item_id` = `user_crafts`.`item_id`
	AND `user_crafts`.`user_id` = '$user_id'
	AND `user_crafts`.`isbest` > 0
	LEFT JOIN craft_buffer2 
	ON `craft_materials`.`item_id` = craft_buffer2.item_id
	AND craft_buffer2.user_id = '$user_id'
	ORDER BY `item_id`");
	$sum = $sumspm = 0;
	foreach($query2 as $key)
	{
		$price = 0;
		$spm2 = intval($key['spm2']);
		$mater = $key['item_id'];
		$mater_need = $key['mater_need'];
		if($mater_need == 0) continue;
		$foundprice = false;
		$mater_name = $key['item_name'];
		$personal = $key['personal'];
		$isbest = $key['isbest'];
		
		//echo $mater_name.'<br>';
		//var_dump($key['buffer_price']);
		if($mater_need > 0)
		$sumspm = $sumspm+$spm2;
		
		if($isbest == 3)
		{
			//if($isbest == 3)
				$price = UserMatPrice($mater,$user_id,1);
			//else
				//$price = $key['craft_price'];
			
			
			$matsum = $mater_need*$price;
			$sum = $sum+$matsum;
			
			continue;
		}
		/*
		if($key['forup_grade'] > 0)//Если ингредиент цветной. 
		{
		
		///Отложим эту функцию до лучших времен.=(
			$grade = $key['forup_grade'];
			//echo '<p>Цветной материал</p>';
		}
		*/	
		if($mater_need < 0)
			$trash = 1;
		if($mater_need < 0 or (!$key['craftable']))
		{
			$user_aucprice = UserMatPrice($mater,$user_id,($mater_need < 0));
			if($user_aucprice)
			{
				$price = $user_aucprice;
				$matsum = $mater_need*$price;
				$sum = $sum+$matsum;
				
				continue;
			}
		}
		
		/*
		if(isset($forlost[$mater]))
			{$price = $forlost[$mater]; /*echo '<p>откопал в промежуточных: '.$mater_name.' '.$price.'</p>';*///}
		/*elseif((!$key['craftable']))
				//$lost[] = $mater;
		*/
		if($key['buffer_price'])
		{
			$price = $key['buffer_price'];
			//echo '<p>откопал в промежуточных: '.$mater_name.' '.$price.'</p>';
		}elseif(!$key['craftable'])
			$lost[] = $mater;
		//$forlostnames[$mater_name] = $mater;
		
		
		
		$matsum = $mater_need*$price;
		$sum = $sum+$matsum;
		
 }
	//echo '<p>Ор-ов: '.$or.' по '.$orcost.'</p>';
	$total = $sum+$or*$orcost;
	$sumspm = round($sumspm/$itog);
	return [round($total/$itog),$sumspm];
};

function MissedList($lost)
{
	$lost = array_unique($lost);
	//printr($lost);
	?><p><b>Возможно, расчет не корректный.</b><br>Я не нашёл следующие цены:<p><?php
	$hrefself = $_SERVER['PHP_SELF'];
	$lostnames = ItemAny($lost,'item_name');
	
	$item_valut = ItemAny($lost,'valut_id');
	
	$valutas = array_unique($item_valut);
	
	$valutas = array_diff($valutas,[500,'']);
	$valutas = array_diff($valutas,[0,'']);
	//printr($valutas);
	if(count($valutas) > 0)
	{
		$valutas = ItemAny($valutas,'item_name');
		//var_dump($valutas);
		//printr($valutas);
		foreach($valutas as $k => $v)
		{
			$item_valut[$k] = 500;
		}
		$lostnames = $valutas + $lostnames;
	}
	
	$lostitems = array_keys($lostnames);
	$lostitems = implode(',',$lostitems);
	$qwe = qwe("
	SELECT 
	item_id,
	item_name,
	icon,
	basic_grade,
	valut_id
	FROM `items`
	WHERE `item_id` IN (".$lostitems.")
	");
	if($qwe->num_rows == 0)
		return false;
	
	foreach($qwe as $q)
	{
		extract($q);
		//echo $item_name.'<br>';
		if($valut_id and $valut_id != 500)
			continue;

		//echo '<a href="/items.php?item_id='.$vl.'" text-decoration: none; style="color: #6C3F00;">'.$lostname.'</a><br>';
		
		PriceCell($item_id,false,$item_name,$icon,$basic_grade);
		
	}
}

function GroupCraft($arritog,$or)
{
	global $lost, $orcost, $itog, $user_id, $forlost;
	extract($arritog);
	$qwe = qwe("
	SELECT `item_name`, `amount`, sum(`amount`) as `sum`
	FROM `craft_groups` 
	WHERE `group_id` = 
	(SELECT `group_id` FROM `craft_groups` WHERE `craft_id` = '$craft_id')");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	$gcr = mysqli_fetch_assoc($qwe);
	$am_sum = $gcr['sum'];
	if(!$am_sum ) return false;
	//var_dump($result_amount);
	$itog = $result_amount;
	$cr_part = $itog/$am_sum;
	$itog = $itog/$cr_part;
	$qwe = qwe("
	SELECT 
	`craft_materials`.`item_id` as mater,
	`craft_materials`.`mater_need`,
	`items`.`craftable`,
	`items`.`personal`,
	`user_crafts`.`isbest`,
	`user_crafts`.`craft_price`
	FROM `craft_materials`
	INNER JOIN `items` 
	ON `craft_materials`.`item_id` = `items`.`item_id`
	AND `craft_materials`.`craft_id` = '$craft_id' 
	AND `craft_materials`.`mater_need` > 0
	LEFT JOIN `user_crafts` 
	ON `items`.`item_id` = `user_crafts`.`item_id`
	AND `user_crafts`.`user_id` = '$user_id'
	AND `user_crafts`.`isbest` > 0
	");
	$sum = 0; $price = 0;
	 foreach($qwe as $gcr)
	 {
		extract($gcr);
		
		if($isbest)
		{
			//echo $mater.' '.$mater_need.'ыапаывапыв</p>';
			if($isbest == 3)
				$price = UserMatPrice($mater,$user_id,1);
			else
				$price = $craft_price;
			
			$price_type = 'gold';
			$matsum = $mater_need*$price;
			$sum = $sum+$matsum;
			continue;
		}
		 
		if(!$craftable)
		{
			
			$user_aucprice = UserMatPrice($mater,$user_id,1);
			if($user_aucprice)
			{
				$price = $user_aucprice;
				$price_type = 'gold';
				$matsum = $mater_need*$price;
				$sum = $sum+$matsum;
				continue;
			}
		}
		

		if(!$price)
		{
			if(isset($forlost[$mater]))
			{
				$price = $forlost[$mater]; 
				//echo '<p>откопал в промежуточных: '.$mater.' '.$price.'</p>';
			}
			else 
			{if(!$craftable) $lost[] = $mater;}

		}
		 
		$matsum = $mater_need*$price;
		$sum = $sum+$matsum;

	}
	//echo '<p>Итм-ов: '.$or.' по '.$orcost.'</p>';
	
	$total = $sum+$or*$orcost;
	
	//echo $total.'<br>';
	 return $total;	
}

function BestCraftWay($query, $user_id, $all_or,$mater_need)
{
	$mats = $crafts = array();
	//static $all_or;
	
	foreach($query as $v)
	{
		$mat_id = $v['item_id'];
		$mats[] = $mat_id;
		$pmater_need = $v['mater_need']*$mater_need;
		$query2 = qwe("SELECT
		`crafts`.`craft_id`,
		`crafts`.`result_item_id`,
		`crafts`.`result_item_name`,
		`crafts`.`labor_need`,
		`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100 / `result_amount` AS `or`,
		`crafts`.`result_amount`,
		`crafts`.`craft_time`,
		`crafts`.`prof_id`,
		`user_crafts`.`isbest`,
		`user_profs`.`lvl`,
		`prof_lvls`.`min`,
		`prof_lvls`.`max`,
		`prof_lvls`.`save_or`,
		`prof_lvls`.`save_time`,
		`profs`.`used`
		FROM 
		`crafts`
		INNER JOIN `user_crafts` ON `crafts`.`craft_id` = `user_crafts`.`craft_id`
		AND `user_crafts`.`user_id` = '$user_id'
		AND `user_crafts`.`item_id` = '$mat_id'
		AND `user_crafts`.`isbest` in (1,2)
		AND `crafts`.`on_off` = 1
		LEFT JOIN `user_profs` ON `user_crafts`.`user_id` = `user_profs`.`user_id`
		AND `crafts`.`prof_id`= `user_profs`.`prof_id`
		LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
		INNER JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
		");
		$arritog = mysqli_fetch_assoc($query2);
		$result_amount = $arritog['result_amount'];
		$craft_id = $arritog['craft_id'];
		$crafts[] = $craft_id;
		$or = $arritog['or'];
		$low_or = $or*$pmater_need/$result_amount;
		$low_or = round($low_or,2);
		$all_or = $all_or + $low_or;
		$all_or = round($all_or,2);
		
		$querymat = qwe("
		SELECT * FROM `craft_materials` 
		WHERE `craft_id` = '$craft_id'
		AND `mater_need`*1 > 0
		AND `item_id` in
		(
			SELECT `item_id` FROM `user_crafts` 
			WHERE `user_id` = '$user_id' 
			AND `isbest` in(1,2)
		)");
		if(mysqli_num_rows($querymat)>0)
		foreach($querymat as $qm)
		{
			$mat_id = $qm['item_id'];
			BestCraftWay($query, $user_id, $all_or,$pmater_need);
		}
		
	echo '<p>'.$arritog['result_item_name'].' | '.$low_or.'</p>';	
	}
	if(count($crafts) > 0)
	{
		$craft_str = implode(',',$crafts);
		//echo $craft_str;
			
			
			
		
		
	}
	return($all_or);

}

function SumOr($item_id, $user_id, $best_arr)
{
	//var_dump($best_arr);
	$bestcraft_str = implode(' ,',$best_arr);
	$query = qwe("SELECT sum(`labor_need`) as `sum_or`,
	`crafts`.`prof_id`,
	`prof_lvls`.`lvl`,
	`prof_lvls`.`save_or`,
	`profs`.`used`,
	round(sum(`labor_need`)- (sum(`labor_need`)*IFNULL(`save_or`,0)/100)*`used`,0) as `total_sumor`
	FROM 
	`crafts` 
	LEFT JOIN `user_profs` ON `user_profs`.`prof_id` =  `crafts`.`prof_id`
	LEFT JOIN `prof_lvls` ON `prof_lvls`.`lvl` = `user_profs`.`lvl`
	LEFT JOIN `profs` ON `profs`.`prof_id` = `user_profs`.`prof_id`
	WHERE `craft_id` IN 
	($bestcraft_str)
	GROUP BY `prof_id`");

	foreach($query as $sumors)
	{
		$sumor = $sumors['sum_or'];
		echo $sumor.'<br>';
	}
	
	
}

function UserTree($query2, $user_id)
{
	
}

function AllOr($craft_id,$user_id,$mater_exponent = 1,$arr_or = [],$allor_deep)
{
global $arr_or, $allor_deep, $mater_exponent;

$query = qwe("SELECT
	`crafts`.`craft_id`,
	`crafts`.`result_item_id`,
	`crafts`.`result_item_name`,
	`crafts`.`labor_need`,
	`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100 AS `or`,
	`crafts`.`result_amount`
	FROM
		`crafts`
	LEFT JOIN `user_profs` ON `user_profs`.`user_id` = '$user_id'
	AND `crafts`.`prof_id`= `user_profs`.`prof_id`
	LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
	INNER JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
	WHERE `crafts`.`craft_id` = '$craft_id'
	");
	
	$arritog2 = mysqli_fetch_assoc($query);
	$result_amount = $arritog2['result_amount'];
	$delimetr = 1;
	//if($allor_deep == 0)
	//	$delimetr = 1;
	//else
		$delimetr = 'current_craft.current_amount';
	//$mater_exponent = $mater_exponent / $result_amount;
	$or = $arritog2['or'];
	//$or = round($or);
	//echo '<p>'.$arritog2['result_item_name'].' | '.$or.' | '.$allor_deep.'</p>';
	//$mater_exponent = 1;
$query2 = qwe("
	SELECT 
	maters.item_id as mat_id,
	maters.mater_need,
	round(maters.mater_need * '$mater_exponent' / current_craft.current_amount,2) as mater_exponent,
	items.item_name,
	user_crafts.craft_id as chcraft_id,
	crafts.result_amount as child_amount,
	`profs`.`prof_id`,
	`profs`.`profession`,
	round(`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100 / crafts.result_amount * maters.mater_need * '$mater_exponent' / ".$delimetr.",2) AS `mat_or`,
	current_craft.current_amount
	from
	(SELECT * 
		FROM `craft_materials`
	WHERE `craft_id` = '$craft_id' AND `mater_need`*1 > 0) as maters
	INNER JOIN items ON maters.item_id = items.item_id
	INNER JOIN user_crafts ON 	user_crafts.item_id = `maters`.item_id
	AND user_crafts.`user_id` = '$user_id' AND user_crafts.`isbest` in(1,2)
	INNER JOIN crafts ON user_crafts.craft_id = crafts.craft_id
	INNER JOIN (SELECT result_amount as current_amount from crafts WHERE craft_id = '$craft_id') as current_craft
	INNER JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
	LEFT JOIN `user_profs` ON `user_crafts`.`user_id` = `user_profs`.`user_id`
	AND `crafts`.`prof_id`= `user_profs`.`prof_id`
	LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
	");
if(mysqli_num_rows($query2)>0)
{
	$allor_deep++;
	
	foreach($query2 as $q2)
	{
		
		extract($q2);
		//echo 'expo '.$item_name.' '.$mater_exponent.'<br>';
		//$mater_exponent = round($mater_exponent,10);
		$arr_or[] = $mat_or;
		//$exponent_maters[] = $mater_exponent;
		AllOr($chcraft_id,$user_id,$mater_exponent,$arr_or,$allor_deep);
		
	}
	
	//printr($arr_or);
	
}//else
//$mater_exponent = 0;
//$arr_or[] = $or;
//$all_or = round($or/$result_amount,2);
//var_dump($arr_or);


//qwe("UPDATE `user_crafts` SET `labor_total` = '$all_ordb' WHERE `user_id` = '$user_id' AND `craft_id`='$craft_id'");	



//return $all_or;
}

function AllOrRecurs($craftarr,$user_id)
{
global $arr_or, $allor_deep, $mater_exponent;
	$arr_or = [];
	foreach($craftarr as $k => $craft_id)
	{
		$query = qwe("SELECT
		`crafts`.`craft_id`,
		`crafts`.`result_item_id`,
		`crafts`.`result_item_name`,
		`crafts`.`labor_need`,
		`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100 AS `or`,
		`crafts`.`result_amount`,
		`crafts`.`spm`
		FROM
			`crafts`
		LEFT JOIN `user_profs` ON `user_profs`.`user_id` = '$user_id'
		AND `crafts`.`prof_id`= `user_profs`.`prof_id`
		LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
		INNER JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
		WHERE `crafts`.`craft_id` = '$craft_id'
		");
	
		$arritog2 = mysqli_fetch_assoc($query);
		$result_amount = $arritog2['result_amount'];
		$or = round($arritog2['or'],2);
		AllOr($craft_id,$user_id,1,$arr_or,0);
		$allor_deep = 0;
		$all_or = array_sum($arr_or);
		$all_or = round($all_or,2);
		$all_or = round(($all_or+$or/$result_amount),2);

		qwe("UPDATE `user_crafts` SET `labor_total` = '$all_or' WHERE `user_id` = '$user_id' AND `craft_id`='$craft_id'");
		//if(in_array($craft_id,[7571,77,4296,9545]))
			//printr($arr_or);
		$arr_or = [];
		$mater_exponent = 1;
	}
}

function CraftsBuffering($craftkeys1)
{
	global $user_id,$complited;
    //$craftarr = [];
	if(!isset($complited))
		$complited = [];
	$mycost = 0;
	//printr($craftkeys1);
	foreach($craftkeys1 as $item_id => $crafts)
	{
		if(array_key_exists($item_id,$complited))
			continue;
		foreach($crafts as $key => $craft_id)
		{
			//$complited[$craft_id];
			$rescost = rescost($craft_id, []);
			$mycost = $rescost[0];
			$matspm = $rescost[1];
			qwe("
			REPLACE INTO `craft_buffer` 
			(user_id, craft_id, craft_price, matspm)
			VALUES
			('$user_id', '$craft_id', '$mycost', '$matspm')
			");
			$craftarr[] = $craft_id;
		}
		//break;
		ToBuffer2($item_id);
		$complited[$item_id] = 1;
	}

	if(isset($craftarr))
    {
        $craftarr =  array_unique($craftarr);
        return $craftarr;
    }
	else
	    return [];
}
function ToBuffer2($item_id)
{
	global $user_id;
	$qwe = qwe("
		SELECT  * , ROUND(if(tmp.kry>0,SQRT(tmp.kry),SQRT(tmp.kry*-1)*-1)) as spmp
		FROM(
		SELECT 
		items.item_id,
		items.item_name,
        items.categ_id,
		crafts.craft_id,
		crafts.dood_name,
		crafts.result_amount,
		craft_buffer.craft_price,
		((crafts.spm+craft_buffer.matspm)*(IFNULL(user_crafts.isbest,1) != 3)) as spm,
		ROUND(SQRT((crafts.spm+craft_buffer.matspm)))*(IFNULL(user_crafts.isbest,1) != 3)*craft_buffer.craft_price+craft_buffer.craft_price as kry,
		crafts.deep,
		user_crafts.isbest,
		craft_buffer.matspm
		FROM crafts
		INNER JOIN craft_buffer
		ON craft_buffer.craft_id = crafts.craft_id
		AND crafts.result_item_id = '$item_id'
		AND craft_buffer.user_id = '$user_id'
		INNER JOIN items ON items.item_id = crafts.result_item_id
		LEFT JOIN user_crafts ON user_crafts.craft_id = crafts.craft_id
		AND user_crafts.user_id = '$user_id'
		AND user_crafts.isbest > 1
		) as tmp
		ORDER BY isbest DESC, deep DESC, item_id, spmp, craft_price, result_amount DESC
		
		");
		if(!$qwe) return;
		$i = 0;
		foreach($qwe as $q)
		{$i++;
			extract($q);
		 	$isbest = intval($isbest);
		 	if($i == 1)
			{
				 qwe("
				REPLACE INTO craft_buffer2
				(user_id,craft_id, item_id, craft_price, spm)
				VALUES
				('$user_id','$craft_id','$item_id','$craft_price','$spm')
				");
				if(!$isbest) $isbest = 1;
			}else
				$isbest = 0;

            if(in_array($categ_id,[133]))
            {
                $pass_labor = PackObject($item_id)['pass_labor2'] ?? 0;
                $labor_price = PriceMode(2,$user_id)['auc_price'] ?? 0;

                $craft_price = $craft_price + $pass_labor*$labor_price;
            }
		 	qwe("
			REPLACE INTO `user_crafts` 
			(`item_id` , `user_id`, `isbest`, `craft_id`, `craft_price`,`spmu`,`updated`) 
			VALUES 
			(
			'$item_id', 
			'$user_id', 
			'$isbest', 
			'$craft_id', 
			'$craft_price',
			'$spmp',
			now()
			)");
		}
}
?>