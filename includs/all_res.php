<?php
function AllResShow($u_amount,$trash = false)
{
	global $User;
	$table = 'craft_all_mats';
	if($trash) 
		$table = 'craft_all_trash';
	$qwe = 
	qwe("
	SELECT 
	`".$table."`.`mat_id`, 
	Sum(`".$table."`.`mater_need`) as `sum`, 
	`items`.`item_name`, 
	`items`.`categ_id`, 
	`items`.`icon`,
	`items`.`basic_grade`
	FROM `".$table."`
	INNER JOIN `items` ON `".$table."`.`mat_id` = `items`.`item_id`
	WHERE `user_id` = '$User->id'
	GROUP BY `mat_id`
	ORDER BY `categ_id`
	");
	$all_mats = [];
	if((!$qwe) or $qwe->num_rows == 0)
		return false;
	ob_start();
	foreach($qwe as $v)
	{
		$micon = $v['icon'];
		$mat_id = $v['mat_id'];
		
		$sum = $v['sum'];
		$basic_grade = $v['basic_grade'];
		$item_name = $v['item_name'];
		$matprice = UserMatPrice($mat_id,$User->id,$sum<0);
		$matprice = esyprice($matprice);
		$matprice = htmlspecialchars($matprice);
		if($mat_id != 500)
			$tooltip = $item_name.'<br>'.round($sum,4).'шт по<br>'.$matprice;
		else
			$tooltip = $item_name.'<br>'.htmlspecialchars(esyprice(round($sum)));
		?>
		<div class="itim" id="itim_<?php echo $mat_id?>" style="background-image: url(/img/icons/50/<?php echo $micon?>.png)">
			<div class="grade" data-tooltip="<?php echo $tooltip?>" style="background-image: url(/img/grade/icon_grade<?php echo $basic_grade?>.png)">
			<div class="matneed"><?php echo round($sum,2)?></div>
			</div>
		</div>
		<?php
	}
	
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

$all_res = AllResShow($u_amount);
if($all_res)
{
	?>
	<br>
	<details class="details"><summary><b>Все требуемые ресурсы для <?php echo $Craft->result_amount*$u_amount?>шт</b></summary><div class="all_res_area">
	<?php echo $all_res?>
	</div></details><br><hr><br>
	<?php	
}

$all_trash = AllResShow($u_amount,1);
if($trash and $all_trash)
{
	?>
	<details class="details"><summary><b>Полученные отходы с <?php echo $Craft->result_amount*$u_amount?>шт</b></summary>
		<div class="all_res_area">
		<?php echo $all_trash ?>
		</div>
	</details><br><hr>
<?php
}
