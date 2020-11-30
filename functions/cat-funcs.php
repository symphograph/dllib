<?php

////Материалы
function all_res($user_id, $mat_id, $mater_need2, $mat_deep)
{  
	global $mat_deep;
	$query = qwe("
	SELECT DISTINCT `crafts`.`craft_id`, `crafts`.`result_amount` 
	FROM `craft_materials`, `crafts` 
	WHERE `craft_materials`.`result_item_id` = '$mat_id'
	AND `craft_materials`.`craft_id` = `crafts`.`craft_id`
	AND `craft_materials`.`craft_id` 
	in (SELECT `craft_id` from `user_crafts`
	WHERE `user_id` = '$user_id' and `isbest` > 0)");
	foreach($query as $v)
	{
		$craft_id = $v['craft_id'];
		$amount = $v['result_amount'];
	}
	$query = qwe("
	SELECT
	`craft_materials`.`item_id`,
	`craft_materials`.`mater_need`,
	/*(min(`craft_materials`.`mater_need`) <0) as `have_trash`,*/
	`items`.`item_name`,
	`items`.`craftable`,
	`item_categories`.`item_group`
	FROM
	`craft_materials`
	INNER JOIN `items` ON `craft_materials`.`item_id` = `items`.`item_id`
	INNER JOIN `item_categories` ON `items`.`categ_id` = `item_categories`.`id`
	WHERE `craft_id` = '$craft_id'
	AND `mater_need` > 0
	/*GROUP BY `craft_materials`.`item_id`*/
	");
	//echo '<ul><details>';
	$rows = mysqli_num_rows($query);
	$i = 0;
	foreach($query as $v)
	{
		$i++;
		$item_group = $v['item_group'];
		$mat_id = $v['item_id'];
		$mat_name = $v['item_name'];
		$mater_need = $v['mater_need'];
		$craftable = $v['craftable'];
		//var_dump($mater_need);
		//if($v['have_trash']) echo '<br>'.$craft_id;
			$best = 0;
			if($craftable > 0)
			{
				$best = UserCraftStatus($user_id,$mat_id);
				
				if($best != 3 and $item_group != 23)
				{
					$mat_deep++; 
					$mater_need = bcdiv($mater_need,$amount,10)*$mater_need2;
					
				 	all_res($user_id, $mat_id, $mater_need, $mat_deep);
				}
			}
			
			if($item_group == 23 or (!$craftable) or $best == 3)
			{
				$mater_need = bcdiv($mater_need,$amount,10)*$mater_need2;;
				qwe("INSERT INTO `craft_all_mats` (`user_id`, `craft_id`, `mat_id`, `mater_need`, `mater_need2`)
				VALUES ('$user_id', '$craft_id', '$mat_id', '$mater_need', '$mater_need2')
				");
				
			}

	}
	//return($mat_deep);
	//echo '</details></ul>';
}

function all_trash($user_id, $mat_id, $mater_need2, $mat_deep)
{  
	global $mat_deep;
	$query = qwe("
	SELECT DISTINCT 
	`crafts`.`craft_id`, 
	`crafts`.`result_amount`
	FROM `craft_materials`
	INNER JOIN `crafts` 
	ON `craft_materials`.`result_item_id` = '$mat_id'
	AND `craft_materials`.`craft_id` = `crafts`.`craft_id`
	INNER JOIN `user_crafts` 
	ON `craft_materials`.`craft_id` = `user_crafts`.`craft_id`
	AND `user_crafts`.`user_id` = '$user_id'
	AND `user_crafts`.`isbest` > 0
	");
	foreach($query as $v)
	{
		$craft_id = $v['craft_id'];
		$amount = $v['result_amount'];
	}
	$query = qwe("
	SELECT
	`craft_materials`.`item_id`,
	`craft_materials`.`mater_need`,
	(min(`craft_materials`.`mater_need`) <0) as `have_trash`,
	`items`.`item_name`,
	`items`.`craftable`,
	`item_categories`.`item_group`
	FROM
	`craft_materials`
	INNER JOIN `items` ON `craft_materials`.`item_id` = `items`.`item_id`
	INNER JOIN `item_categories` ON `items`.`categ_id` = `item_categories`.`id`
	WHERE `craft_id` = '$craft_id'
	GROUP BY `craft_materials`.`item_id`
	");
	//echo '<ul><details>';
	$rows = mysqli_num_rows($query);
	//var_dump($rows);
	$i = 0;
	foreach($query as $v)
	{
		$i++;
		$item_group = $v['item_group'];
		$mat_id = $v['item_id'];
		$mat_name = $v['item_name'];
		$mater_need = $v['mater_need'];
		$craftable = $v['craftable'];
		
		//if($v['have_trash']) echo '<br>'.$mat_id;
		$best = 0;
		if($craftable and $mater_need >0)
		{
			$best = UserCraftStatus($user_id,$mat_id);

			if($best != 3)
			{
				$mat_deep++; 
				$mater_need = bcdiv($mater_need,$amount,10)*$mater_need2;

				all_trash($user_id, $mat_id, $mater_need, $mat_deep);
			}
		}

		if($mater_need<0)
		{
			//echo $mat_id.' '.$mater_need.'<br>';
			$mater_need = bcdiv($mater_need,$amount,10)*$mater_need2;
			$mater_need = abs($mater_need);
			qwe("INSERT INTO `craft_all_trash` (`user_id`, `craft_id`, `mat_id`, `mater_need`)
			VALUES ('$user_id', '$craft_id', '$mat_id', '$mater_need')
			");

		}
	}
	//return($mat_deep);
	//echo '</details></ul>';
}

?>