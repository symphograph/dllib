
<?php
function deepfound(bool|PDOStatement $query,$lvl, $errarr, $root_craft)
{	global $errcnt, $errarr;
	$mats =array();
		$cnt = $query->rowCount();
		//echo '<p>Вижу '.$cnt.' компонентов</p>';
   foreach($query as $v)
	{
		 //$mater_need = $v['mater_need'];
		//if($mater_need < 0) continue;
		$mat_id = $v['item_id'];
	   	//if(!IsCraftable($mat_id)) 
		$mat_name = $v['item_name'];
		$cr_name = $v['result_item_name'];
		$mats[]= $mat_id;
		$craft_id = $v['craft_id'];

		 qwe("REPLACE INTO `craft_lvls` (`lvl`, `item_id`, `item_name`) 
		 VALUES ('$lvl', '$mat_id', :mat_name)",
		 ['mat_name' => $mat_name]
		 );
		qwe("REPLACE INTO `craft_tree` (`root_craft_id`, `craft_id`) 
		VALUES ('$root_craft', '$craft_id')");
	}
	if(count($mats)>0)
	{
		$lvl++;
		if($lvl>30) 
		{ 


			 $er_row = $mat_id.' '.$cr_name;
			 if(!in_array($er_row,$errarr)) 
				{ $errarr[] = $er_row;
				 $errcnt++;
				  echo '<p>Превышен лимит итераций для '.$er_row.'</p>';
				  echo 'Для '.$cr_name.' надо '.implode(', ',$mats);
				}
		   return;
		};
		
		$imp = implode(', ',$mats);
		$query = qwe("SELECT DISTINCT `item_id`, `item_name`, `result_item_name`, `craft_id` 
		FROM `craft_materials` 
		WHERE `result_item_id` in ($imp)
		AND `craft_id` in (SELECT `craft_id` FROM `crafts` where `on_off` = 1) 
		AND `item_id` in (SELECT `result_item_id` FROM `crafts` where `on_off` = 1)
		AND `mater_need` >0");	
		deepfound($query, $lvl, $errarr, $root_craft);	
	}
}

function IsCraftable($item_id)
{
	$qwe = qwe("
	SELECT * FROM crafts 
	WHERE result_item_id = '$item_id'
	AND on_off
	");
	if(!$qwe or $qwe->rowCount() == 0)
		return false;
	foreach($qwe as $q)
	{
		$crafts[] = $q['craft_id'];
	}
	return $crafts;
}

function MatsFound()
{
	
}
?>		
