<?php

function MissedList($lost)
{
	$lost = array_unique($lost);
	global $User;
	?>
    <div>
        <br><b>Расчет не получился.</b>
        <br>В дочерних рецептах есть неизвестные цены.
        <br>Без них я не могу посчитать и сравнить.
    </div>
    <?php

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
    craftable,
	icon,
	basic_grade,
	valut_id
	FROM `items`
	WHERE `item_id` IN (".$lostitems.")
	");
	if(!$qwe or !$qwe->rowCount())
		return false;
	$newlost = [];
	foreach($qwe as $q)
	{
		$q = (object) $q;
		if($q->valut_id and $q->valut_id != 500)
			continue;

		PriceCell($q->item_id,$q->item_name,$q->icon,$q->basic_grade);

		if(!$q->craftable and $User->mode == 1){
            qwe("
            REPLACE INTO lost_items 
            (user_id,item_id, server_group, detetime) 
            VALUES 
           ($User->id, $q->item_id, $User->server_group, now())
           ");
        }
        $newlost[] = $q->item_id;
	}
	return $newlost;
}

function ToBuffer2($item_id)
{
	global $User;
	$orcost = $User->orCost();

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
            $craft_price = $q->craft_price;
		 	$isbest = intval($q->isbest);
		 	if($i == 1)
			{
				 qwe("
				REPLACE INTO craft_buffer2
				(user_id,craft_id, item_id, craft_price, spm)
				VALUES
				('$User->id','$q->craft_id','$item_id','$craft_price','$q->spm')
				");
				if(!$isbest) $isbest = 1;
			}else
				$isbest = 0;

            if(in_array($q->categ_id,[133,171]))
            {
                $pass_labor = PackObject($item_id)->pass_labor2 ?? 0;
                $craft_price = $q->craft_price + $pass_labor*$orcost;
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
			'$craft_price',
			'$q->spmp',
			now()
			)");
		}
}
?>