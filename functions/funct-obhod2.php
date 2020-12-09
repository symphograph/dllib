﻿<?php
function CraftsObhod($item_id, $user_id)
{
	global $lost, $orcost;
    $MainItem = new Item;
    $MainItem->getFromDB($item_id);
    $craftkeys1 = $MainItem->CraftsByDeep();

    if(!isset($lost))
        $lost = [];


    if(count($craftkeys1))
    {
        if(!isset($orcost))
            $orcost = PriceMode(2,$user_id)['auc_price'] ?? false;

        $craftarr = CraftsBuffering($craftkeys1);
        //printr($craftarr);

        if(!in_array($_SERVER['SCRIPT_NAME'],[
            '/hendlers/packs_list.php',
            '/hendlers/isbuysets.php',
            '/packres.php',
            '/hendlers/packpost/packpostmats.php',
            '/hendlers/packpost/packobj.php',
        ]))
        {

            if(count($lost)>0)
            {
                MissedList($lost);
                exit();
            }
        }
    }
    if(isset($craftarr))
        AllOrRecurs($craftarr,$user_id);
}

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



function UserTree($query2, $user_id)
{
	
}

function AllOr(int $craft_id,int $user_id,$mater_exponent = 1,$arr_or = [],$allor_deep = 0)
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

	$or = $arritog2['or'];

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
	round(`labor_need` * (100 - IFNULL(`save_or`,0)*`used`) / 100 / crafts.result_amount * maters.mater_need * '$mater_exponent' / current_craft.current_amount,2) AS `mat_or`,
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

    }
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
	global $User,$complited;
	if(!isset($complited))
		$complited = [];


	//printr($craftkeys1);
	foreach($craftkeys1 as $item_id => $crafts)
	{
		if(array_key_exists($item_id,$complited))
			continue;
		foreach($crafts as $key => $craft_id)
		{
            $Craft = new Craft($craft_id);
            $Craft->InitForUser($User->id);


			$rescost = $Craft->rescost($User->id);
			$mycost = $rescost[0];
			$matspm = $rescost[1];
			qwe("
			REPLACE INTO `craft_buffer` 
			(user_id, craft_id, craft_price, matspm)
			VALUES
			('$User->id', '$craft_id', '$mycost', '$matspm')
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
	global $User;
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
		AND craft_buffer.user_id = '$User->id'
		INNER JOIN items ON items.item_id = crafts.result_item_id
		LEFT JOIN user_crafts ON user_crafts.craft_id = crafts.craft_id
		AND user_crafts.user_id = '$User->id'
		AND user_crafts.isbest > 1
		) as tmp
		ORDER BY isbest DESC, deep DESC, item_id, spmp, craft_price, result_amount DESC
		
		");
		if(!$qwe) return;
		$i = 0;
		foreach($qwe as $q)
		{$i++;
			$q = (object) $q;
		 	$isbest = intval($q->isbest);
		 	if($i == 1)
			{
				 qwe("
				REPLACE INTO craft_buffer2
				(user_id,craft_id, item_id, craft_price, spm)
				VALUES
				('$User->id','$q->craft_id','$item_id','$q->craft_price','$q->spm')
				");
				if(!$isbest) $isbest = 1;
			}else
				$isbest = 0;

            if(in_array($q->categ_id,[133]))
            {
                $pass_labor = PackObject($item_id)['pass_labor2'] ?? 0;
                $labor_price = PriceMode(2,$User->id)['auc_price'] ?? 0;

                $craft_price = $q->craft_price + $pass_labor*$labor_price;
            }
		 	qwe("
			REPLACE INTO `user_crafts` 
			(`item_id` , `user_id`, `isbest`, `craft_id`, `craft_price`,`spmu`,`updated`) 
			VALUES 
			(
			'$item_id', 
			'$User->id', 
			'$isbest', 
			'$q->craft_id', 
			'$q->craft_price',
			'$q->spmp',
			now()
			)");
		}
}
?>