
<div class="chance_row">

<div class="itemprompt" data-title="Проверка успеха выполняется последовательно">
<div class="chance_names">Шанс успеха</div>
<div class="chance_names">Шанс уничтожения</div>
<div class="chance_names">Шанс ухудшения</div>
	</div>
<div class="chance"><?php echo $chance2.' %';?></div>
<div class="chance"><?php echo $brake_ch.' %';?></div>
<div class="chance"><?php echo $delvl_ch.' %';?></div>
</div>
<!--</form>
<form name="chance_cost" action="enchant.php" method="post">-->
<?php
$open = 'open="open"';
if($cost > 0 and ($item_pr > 0 or $grade < 7) and $roll_pr>0) $open = '';
echo
'<details '.$open.' style="margin-left: 50px;"><summary><div class="itemprompt" data-title="Нажми меня"><b>Обязательные параметры</b></div></summary>';

include 'functions/ench_item_price.php';
$what_save = 'click';

price_row($cost, $what_save);
$what_save = 'roll';
price_row($roll_pr, $what_save);
if($catal>0)
{
$what_save = 'catal';
price_row($catal_pr, $what_save);
}
if($grade > 6 and $save == 0)
{
$what_save = 'item';
price_row($item_pr, $what_save);
}
echo
'<br><input type="submit" class="button" value="Сохранить" name="save_params"/>';
?>
</details>
<div class="itog_row">
<?php 
	include 'functions/esyprice.php';
	
	?>
	<div class="itemprompt" data-title="Где искать цену свитка?">
	<input type="radio" name="auc_craft" value="1" <?php echo $a_cr_sel_1;?> onchange="this.form.submit()">Предпочитать Аукцион
	<input type="radio" name="auc_craft" value="2" <?php echo $a_cr_sel_2;?> onchange="this.form.submit()">Предпочитать Себестоимость</div><br>
	<div class="forecast_cost">Расходы на 1 попытку: <?php esyprice($click_cost) ?></div>
	<div class="forecast_cost">Среднеожидаемые затраты (на этот шаг): <?php esyprice($click_all_cost) ?></div>
	<?php 
	if($real_cost > 0)
	{echo '<div class="forecast_cost">Всего потрачено:';  esyprice($real_cost); echo '</div>';} 
	?>
	<input type="hidden" name="chance" value="<?php echo round($chance*10000);?>"/>
	</div>
	<div class="button_row">
	<?php if(!$brake) echo
	'<input class="button button2" value="Ok" type="submit" name="try_start"/>';
		?>
	</form>
	<?php 
//if($dlvl)
//echo "<script>document.getElementsByTagName('form')[0].submit();</script>";
?>
	<form method="post" action="enchant.php">
	<input type="submit" class="button button2" value="Сброс" name="reset"/></div>

</div>

