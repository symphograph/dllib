<?php
//var_dump($_POST);
$item_id = intval($_POST['item_id']);
if(!$item_id) die;
$cooktime = time()+60*60*24*360;
setcookie("item_id",$item_id,$cooktime,'/');
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include_once '../functions/functions.php';
include_once '../functions/functs.php';
include_once '../cat-funcs.php';
include_once '../includs/config.php';

$userinfo_arr = UserInfo();
if(!$userinfo_arr)
	die('<span style="color: red">Oh!<span>');
//printr($userinfo_arr);
extract($userinfo_arr);
$user_id = $muser;

$qwe = qwe("
SELECT
items.item_id,
items.item_name,
items.description,
items.craftable,
items.ismat,
items.basic_grade,
items.icon,
items.categ_id,
items.personal,
items.ismat,
item_categories.item_group,
item_categories.`name` as category,
is_trade_npc,
items.valut_id,
valutas.valut_name,
price_buy,
`item_subgroups`.`sgr_id`
FROM
items
INNER JOIN item_categories ON items.categ_id = `item_categories`.`id`
AND `items`.`on_off` = 1 AND `items`.`item_id` = '$item_id'
LEFT JOIN `valutas` ON `valutas`.`valut_id` = `items`.`valut_id`
LEFT JOIN `item_groups` ON `item_groups`.id = item_categories.item_group
LEFT JOIN `item_subgroups` ON `item_subgroups`.sgr_id = `item_groups`.sgr_id
");
if(!$qwe or $qwe->num_rows == 0) die('Не вижу данных');
foreach($qwe as $q)
	extract($q);

//echo mb_detect_encoding($description);
$description = mysqli_real_escape_string($dbLink,$description);
//$regex = '/((?<=[а-я])[А-Я]|[А-Я](?=[а-я]))/ui';
//$regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
$regex = '/((?<=\p{Ll})\p{Lu}|\p{Lu}(?=\p{Ll}))/ui';
$description = preg_replace( $regex, ' $1', $description );
$description = preg_replace("/ {2,}/"," ",$description);
//$description = replace($description, ' ', $regex  );
	?>
<div id="catalog_area">
	<div class="item_descr_area">
		<?php if($myip) echo $item_id?>
		<div class="nicon">
			<div class="itim" id="itim_<?php echo $item_id?>" style="background-image: url(/img/icons/50/<?php echo $icon?>.png)">
				<div class="grade" style="background-image: url(/img/grade/icon_grade<?php echo $basic_grade?>.png)"></div>
			</div>
			<div class="itemname">
				<div id="mitemname"><b><?php echo $item_name?></b></div>
				<div class="comdate"><?php if($personal) echo 'Персональный предмет'?></div>
				<div class="mcateg" id="categ_<?php echo $categ_id?>" sgroup="<?php echo $sgr_id?>"><?php echo $category?></div>
			</div>	
		</div>
		<hr><br>
		<details><summary>Описание</summary>
		<div class="item_descr"><?php echo $description?></div>
		</details><br>
		
		<a href="https://archeagecodex.com/ru/item/<?php echo $item_id?>/" target="_blank">
		<div class="aacodex_logo" data-tooltip="Смотреть на archeagecodex"></div>
		</a>
		<hr>
		
		<?php 
		if($is_trade_npc)
		{
			
			echo 'Продается у NPC:<br>';
			if($valut_id == 500)
				echo esyprice($price_buy);
			else
			{
				echo $price_buy;
				?><a href="catalog.php?item_id=<?php echo $valut_id?>">
		<img src="img/icons/50/<?php echo IconLink($valut_id)?>.png" width="15" height="15" alt="<?php echo $valut_name?>"/></a><br><br><?php
					
				if(!$personal)
				MoneyForm($item_id);	
			}
			
		}elseif($categ_id != 133)
		{
			
			MoneyForm($item_id);	
		}
		
		if($myip)
		{
			?><a href="edit/recedit.php?addrec=<?php echo $item_id?>" target="_blank"><button class="def_button">Добавить рецепт</button></a><br><?php
			?><a href="edit/edit_item.php?item_id=<?php echo $item_id?>"><button class="def_button">Править итем</button></a><br><?php
			?><a href="edit/item_off.php?item_id=<?php echo $item_id?>"><button class="def_button">отключить</button></a><br><?php
		}
		?>
	<hr><br><a href="user_customs.php"><button class="def_button">Настройки</button></a>	
	</div>
	<div id="catalog_right">
<?php
if($ismat)	
{
	?><p><b>Используется в рецептах:</b></p>
		<div class="up_craft_area"><?php
	UpCraftList($item_id);
		?></div><?php
}
else 
	echo '<div class="up_craft_area">Не используется в рецептах</div>';
			 
			 
			 

	
if($craftable)
{
	?><hr>
	<div class="dcraft_craft_area" id="dcraft_craft_area">
	
	<?php
	//Надо посчитать оптималный
	$globalitem_id = $item_id;
	$trash = false;
	$prof_q = qwe("SELECT * FROM `user_profs` WHERE `user_id` = '$user_id'");
	qwe("DELETE FROM craft_buffer WHERE `user_id` = '$user_id'");
	qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$user_id'");
	include '../edit/funct-obhod2.php';
	include '../includs/recurs.php';
	
	DwnCraftList($globalitem_id);
	?></div><?php
}else
{
	$refuse = IsRefuse($item_id);
	if($refuse)
		RefuseList($refuse);
	else
		echo 'Некрафтабельно';
	if(($is_trade_npc and $valut_id !=500) or  in_array($item_id,[3,4,5,6,23633]))
		ValutInfo($q);
}
	
	
?></div><?php	

?></div><?php	

?></div><?php	
Comments($userinfo_arr,$item_id);

function ValutInfo($q)
{
	global $user_id;
	extract($q);
	$mvalut = $valut_id;
	//Касательно Чести, Рем Репутации, итд.
	if(in_array($item_id,[2,3,4,5,6,23633]))
	{
		$valut_name = $item_name;
		$mvalut = $item_id;
			
	}
	$val_link = '<a href="catalog.php?item_id='.$mvalut.'">
		<img src="img/icons/50/'.$mvalut.'.png" width="15" height="15" alt="'.$valut_name.'"/></a>';
	
	ItemsFromAlterValutes($mvalut);
	?>
	<br><br>
	<h3><?php echo $item_name?>: конвертация в золото.</h3>
	*Следует учесть, что калькулятор не принимает в расчет предметы, позволяющие конвертировать валюту оптом.<br>
	Медиана:
	
	<?php
	//$serv_median = RepMedian($mvalut, $user_id);
	$valutData = MonetisationList($val_link, $mvalut, $user_id);
	//printr($valutData);
	if(!empty($valutData[1]))
	{
		echo '<div class="valut_median">'.$val_link.' = '. esyprice(round($valutData[0],0)).'</div>';
		?>
		<div class="clear"></div><br><hr>
		
		<?php
		echo $valutData[1];
		//var_dump($arr_mon);
		
	}	
	else
		echo '<br>Нет данных. Попробуйте указать несколько цен на предметы за эту валюту.<br>';
	
}

function MoneyForm($item_id)
{
	global $user_id;
	$auc_price = false;
	$myprice = false;
	$prarr = PriceMode($item_id,$user_id);
	$text = '';
	
	$color = '';
	$time = '';
	//var_dump($prarr);
	if($prarr)
	{
		$puser_id = $prarr['user_id'];
		$prnick = AnyById($puser_id,'mailusers','user_nick')[$puser_id];
		$time = $prarr['time'];
		$time = date('d.m.Y H:m',strtotime($time)).'<br>';
		$auc_price = $prarr['auc_price'];
		//var_dump($prnick);
		if($prarr['user_id'] == $user_id)
		{
			$color = 'style="color: darkgreen"';
			$text = '<a href="user_prices.php" data-tooltip="Все мои цены">Вы указали: </a>';
		}elseif($prnick)
		{
			$text = '<a href="user_prices.php?puser_id='.$puser_id.'" data-tooltip="Смотреть его(её) цены">'.$prnick.'</a> указал: ';
			
			if(IsFolow($user_id,$puser_id))
				$color = 'style="color: darkgreen"';	
		}else
			$text = 'Кто-то указал: ';
		
				
	}else
		$text = 'Цена: ';

?>
<span <?php echo $color?>><?php echo $time.$text?></span>
<form id="pr_<?php echo $item_id?>"><div class="money_area_down">

	<?php MoneyLineBL($auc_price,$item_id,'',$myprice);?>
	<span id="PrOk_<?php echo $item_id?>"></span>	
	</div>
	
</form>
<?php
}

function IconLink($item_id)
{
	$qwe = qwe("
	SELECT `icon` FROM `items`
	WHERE `item_id` = '$item_id'
	");
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	return $icon;
}

function RefuseList($items)
{
	
	$items = implode(',',$items);
	$qwe = qwe("
	SELECT
	items.item_id,
	items.icon,
	items.item_name,
	items.basic_grade
	FROM items 
	WHERE item_id in (".$items.")
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	?><p><b>Является отходом при крафте:</b></p>
		<div class="up_craft_area"><?php
	Cubiki($qwe);
	?></div><?php
}

function IsRefuse($item_id)
{
	$qwe = qwe("
	SELECT * 
	FROM craft_materials
	WHERE item_id = '$item_id'
	AND mater_need < 0
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	$arr = [];
	foreach($qwe as $q)
	{
		extract($q);
		$arr[] = $result_item_id;
	}
	return $arr;
}

function DwnCraftList($item_id)
{
	$best_types = ['','Выбран руру','Выбран вами', 'Покупается'];
	
	global $user_id, $mat_deep, $myip, $trash;
	$money = 0;
	$qwe = qwe("
	SELECT
	`crafts`.`craft_id`,
	`crafts`.`dood_id`,
	`crafts`.`dood_name`,
	`crafts`.`result_item_id`,
	`crafts`.`result_item_name`,
	`crafts`.`rec_name`,
	`crafts`.`labor_need`,
	round(`labor_need` * (100 - IFNULL(`save_or`,0)) / 100,0) AS `labor_need2`,
	round(`labor_need` * (100 - IFNULL(`save_or`,0)) / 100/ `result_amount`,2) AS `labor_single`,
	`crafts`.`result_amount`,
	`crafts`.`craft_time`,
	`crafts`.`prof_id`,
	`crafts`.`mins`,
	`user_crafts`.`craft_price`,
	`user_crafts`.`isbest`,
	`user_crafts`.`labor_total`,
	`user_crafts`.`spmu`,
	(SELECT SUM(spmu) FROM user_crafts WHERE `user_id` = '$user_id' AND item_id = '$item_id') as sumspm,
	`crafts`.`rec_name`,
	`user_profs`.`lvl`,
	`prof_lvls`.`min`,
	`prof_lvls`.`max`,
	`prof_lvls`.`save_or`,
	`prof_lvls`.`save_time`,
	`profs`.`profession`,
	`profs`.`used`,
	`items`.`personal`,
	`items`.`categ_id`
	FROM
		`crafts`
	INNER JOIN `user_crafts` ON `crafts`.`craft_id` = `user_crafts`.`craft_id`
	AND `user_crafts`.`user_id` = '$user_id'
	AND `user_crafts`.`item_id` = '$item_id'
	AND `crafts`.`on_off` = 1
	LEFT JOIN `user_profs` ON `user_crafts`.`user_id` = `user_profs`.`user_id`
	AND `crafts`.`prof_id`= `user_profs`.`prof_id`
	LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
	INNER JOIN `profs` ON `profs`.`prof_id` = `crafts`.`prof_id`
	INNER JOIN `items` ON `items`.`item_id` = `crafts`.`result_item_id`
	
	AND `items`.`on_off` = 1
	ORDER BY `isbest` DESC, `spmu`,`craft_price`
	");
	$i=0;
	$imgor = '<img src="../img/icons/50/2.png" width="15px" height="15px"/>';
	foreach($qwe as $q)
	{$i++;
		global $icon, $basic_grade;
		extract($q);
	 	
	 	if(!$basic_grade) $basic_grade = 1;
		$craft_name = $rec_name ?? $result_item_name;
	 	//$u_amount = $result_amount;
	 	$u_amount = 1;
	 	if($i == 1)
		{
			if(!empty($_POST['u_amount']))
			{
				$u_amount = intval($_POST['u_amount']);
			}	
		}
	 
	 	if($i == 1 and !$personal)
		{
			
			//$u_amount = intval($u_amount);
		//if(!$u_amount) 
			//var_dump($u_amount);
			
		 //var_dump($u_amount);
			?>
			<div id="isbuy">
				<div class="isby">
					<input type="radio" <?php if($isbest != 3) echo 'checked';?> id="ib_<?php echo $item_id?>" name="isbuy" value="1"/>
					<label class="navicon" for="ib_<?php echo $item_id?>" style="background-image: url(../img/profs/Обработка_камня.png);"></label>
					<span>Крафтить</span>
				</div>
				<div>x
				<input type="number" name="u_amount" id="u_amount" min="1" value="<?php echo $u_amount?>" autocomplete="off"	/>
				</div>
				<div class="isby">
					<span>Покупать</span>
					<input type="radio" <?php if($isbest == 3) echo 'checked';?> id="is_<?php echo $item_id?>" name="isbuy" value="3"/>
					<label class="navicon" for="is_<?php echo $item_id?>" style="background-image: url(../img/perdaru2.png);"></label>
				</div>
			</div>
			<?php
		}
	 	if($i == 2)
			echo '<br><details><summary>Другие рецепты</summary>';
			
		$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
	 	//var_dump($auc_price);
	 	if($auc_price)
		{
			$profit = round(($auc_price*0.9 - $craft_price),0);
			$labor_total = floatval($labor_total);
			if($labor_total)
			{
				$profitor = round(($profit/$labor_total),0);
				$profitor = esyprice($profitor);
			}	
			else
				$profitor = '';
			$profit = esyprice($profit);	
		} else
		$profitor = $profit = 'Не вижу цену';
	 	$sptime = '';
	 	if($mins)
		{
			$m = $mins;
			$h = floor($m/60);
			$m = $m-$h*60;

			$d = floor($h/24);
			$h = $h-$d*24;
			if($d>0) 
				$d = $d.'д.+';
			else 
				$d = '';
			$sptime = $d.$h.':'.$m;
		}
		
		?>
		<div class="crresults">
			<div><b><?php echo $best_types[$isbest];?></b></div>
			<div>
				<!--<div class="itemprompt" data-title="Рецепт будет выбран автоматически">Сбросить выбор</div>-->
			</div>
		</div>
		<div class="craftinfo">
			<div>
				<div class="crresults"><div></div></div>	
			</div>
			<div>
				<div class="crresults"><div><b><?php echo $craft_name?></b></div></div>	
			</div>
		</div>
		<div class="craftinfo">
			<div>
				<div class="crresults"><div><img src="img/profs/<?php echo str_replace(" ","_",$profession)?>.png" width="20px" height="20px"><?php echo $profession?></div></div>	
			</div>
			<div>
				<div class="crresults"><div><?php echo $dood_name?></div></div>	
			</div>
		</div>
		<div class="craftinfo">
			<div data-tooltip="Коэфициент приводящий друг к другу такие величины, как занимаемая площадь, интервал между сбором, себестоимость, получаемое количество, требуемое количество.">
				<div class="crresults"><div><b>Коэф SPM:</b></div><div><b><?php echo $spmu?></b></div></div>	
			</div>
			<div>
				<div class="crresults"><div>Интервал:</div><div><?php echo $sptime?></div></div>	
			</div>
		</div>
		<div class="craftinfo">
			<div>
				<div class="crresults"><div>Себестоимость 1 шт:</div><div><?php echo esyprice($craft_price);?></div></div>
					<?php 
	 			if(in_array($categ_id,[133]))
				{
					$PackObject = PackObject($item_id);
					?>
					
					<?php
				}else
				{
					
					?>
					<div class="crresults"><div>Прибыль:</div><div><?php echo $profit;?></div></div>
					<div class="crresults"><div>Прибыль с 1 ОР:</div><div><?php echo $profitor?></div></div>
					<?php			
				}
			
			?>
			</div>
			<div>
				<div class="crresults">
					<div>На рецепт:</div>
					<div><?php echo $labor_need2.$imgor;?>
						
					</div>
				</div>
				<?php 
	 			if(in_array($categ_id,[133]))
				{
					 
					$pass_labor = $PackObject['pass_labor'];
					?>
					<div class="crresults">
						<div>На сдачу:</div>
						<div><?php echo $pass_labor.$imgor;?></div>
					</div>
					<div class="crresults"><div>На всё:</div><div><?php echo round($labor_total+$pass_labor,2).$imgor;?></div></div>
					<?php
				}else
				{
					?>
					<div class="crresults"><div>На 1 шт:</div><div><?php echo $labor_single.$imgor;?></div></div>
					<div class="crresults"><div>На цепочку:</div><div><?php echo round($labor_total,2).$imgor;?></div></div>
					<?php
				}
				?>
			</div>
		</div>
		<div class="crftarea">
		
		<?php
	 	$dtitle = ''; 
		$qwe2 = qwe("
		SELECT 
		craft_materials.item_id,
		craft_materials.mater_need,
		craft_materials.mat_grade as basic_grade,
		items.item_name,
		items.icon,
		items.craftable
		FROM `craft_materials`
		INNER JOIN items ON items.item_id = craft_materials.item_id
		AND craft_materials.craft_id = '$craft_id'
		");
		if($qwe2->num_rows > 0)
		{
			//var_dump($isbest);
			if($isbest > 1)
			{
				//$craft_id = 0;
				$dtitle = 'Сбросить';
			}else
			{
				if($qwe->num_rows>1 and (!$isbest))
				$dtitle = 'Предпочитать этот';
			}
				
				
			
			?>
			<div class="main_itim" id="cr_<?php echo $craft_id?>" name="<?php echo $item_id?>" style="background-image: url(/img/icons/50/<?php echo $icon?>.png)">
				<div class="grade" data-tooltip="<?php echo $dtitle?>" style="background-image: url(/img/grade/icon_grade<?php echo $basic_grade?>.png)">
					<div class="matneed"><?php echo $result_amount*$u_amount?></div>
				</div>
			</div>
			<div class="matarea">
			<div class="matrow">
			<?php
			//$divisor = $result_amount/$u_amount;
			$money = MaCubiki($qwe2,$u_amount,$craft_price);
			?></div><?php
			if($money)
			{
				?><div class="crmoney"><?php echo esyprice($money);?></div><?php
			}
		?></div><?php
		}	
		else
			echo 'Не нашел материалы';
		?></div><?php
	 	if($myip)
		{
			?><a href="edit/recedit.php?query=<?php echo $craft_id?>" target="_blank">Править</a><?php
		}
		?>
		<hr><?php
		if($i == 1)
		//Если рецепт основной, показываем полный список ресов	
		{		
			qwe("DELETE FROM `craft_all_mats` WHERE `user_id` = '$user_id'");
			qwe("DELETE FROM `craft_all_trash` WHERE `user_id` = '$user_id'");
			$mats = array();
			?><div><?php
			all_res($user_id, $item_id, $result_amount*$u_amount, $mat_deep);
			if($trash)
			all_trash($user_id, $item_id, $result_amount*$u_amount, $mat_deep);
			
			//if($mat_deep > 1)
				include '../all_res.php';
			qwe("DELETE FROM `craft_all_mats` WHERE `user_id` = '$user_id'");
			qwe("DELETE FROM `craft_all_trash` WHERE `user_id` = '$user_id'");
			?></div><div class="clear"><?php
		}
	}
	

}
function PackObject($item_id)
{
	global $user_id;
	$qwe = qwe("
	SELECT
packs.zone_id,
packs.pack_sname,
pack_types.pack_t_name,
pack_types.pack_t_id,
pack_types.pass_labor,
zones.zone_name,
zones.side,
round(`pass_labor` * (100 - IFNULL(`save_or`,0)) / 100,0) AS `pass_labor2`
FROM
packs
INNER JOIN pack_types ON packs.item_id = '$item_id' 
AND pack_types.pack_t_id = packs.pack_t_id
INNER JOIN zones ON packs.zone_id = zones.zone_id
LEFT JOIN `user_profs` ON `user_profs`.`user_id` = '$user_id'
AND `user_profs`.`prof_id` = 5
LEFT JOIN `prof_lvls` ON `user_profs`.`lvl` = `prof_lvls`.`lvl`
	");
	if(!$qwe or $qwe->num_rows == 0) 
		return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	return ['pass_labor'=> $pass_labor2];
}
function MaCubiki($qwe,$u_amount,$craft_price)
{
	global $user_id;
	$i = $money = 0;
	$flowers = [2178, 3564, 3622,3627,3628,3659,3667,3671,3680,3684,3685,3711,3713,8009,14629,14630,14631,16268,16273,16290];
	
	foreach($qwe as $q)
	{$i++;
		extract($q);
		 if(!$basic_grade) $basic_grade = 1;
		 if($item_id == 500)
		 {
			 $money = $mater_need;
			 continue;
		 }
		 if($mater_need < 0 and in_array($item_id,$flowers))
			$matprice =	$craft_price;
			else 
		$matprice = UserMatPrice($item_id,$user_id);
		$matprice = esyprice($matprice);
		$matprice = htmlspecialchars($matprice);
		 //var_dump($divisor);
		$mater_need = round($mater_need*$u_amount,2);
		$tooltip = $item_name.'<br>'.$mater_need.' шт по<br>'.$matprice;

		Cubik($item_id,$icon,$basic_grade,$tooltip,$mater_need);
	}
	return $money;
}



function ValutToGold($item_id)
{
	$qwe = qwe("
	SELECT * FROM `items` 
	WHERE `item_id` = '$item_id'
	");
	if(!$qwe) return false;
	if($qwe->num_rows == 0) return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	
}

function UpCraftList($item_id)
{

	$qwe = qwe("
	SELECT DISTINCT
	craft_materials.result_item_id as `item_id`,
	items.icon,
	items.item_name,
	items.basic_grade
	FROM
	craft_materials
	INNER JOIN items ON craft_materials.result_item_id = items.item_id
	AND craft_materials.item_id = '$item_id'
	AND items.on_off = 1
	AND craft_materials.mater_need > 0
	INNER JOIN crafts ON craft_materials.craft_id = crafts.craft_id
	AND crafts.on_off = 1
	ORDER BY crafts.deep DESC,
	items.categ_id, craft_materials.result_item_id
	");
	if($qwe->num_rows > 0)
		Cubiki($qwe);
	else
		echo 'Не нашел рецепты';
}

function Cubiki($qwe)
{
	foreach($qwe as $q)
	{
		extract($q);
		if(!$basic_grade) $basic_grade = 1;
		?>
		
		
			<div class="itim" id="itim_<?php echo $item_id?>" style="background-image: url(/img/icons/50/<?php echo $icon?>.png)">
				<div class="grade" class="grade" data-tooltip="<?php echo $item_name?>" style="background-image: url(/img/grade/icon_grade<?php echo $basic_grade?>.png)">
					
				</div>
			</div>
		
		
		<?php
	}
}
			 
function LongestWordFound($text)
{
	if((!is_string($text)) or empty($text)) 
		return false;
	
	$arr = array_flip(explode(' ', $text));

	// определяем длину
	foreach ($arr as $word => $length) {
		$arr[$word] = mb_strlen($word);
	}

	// сортируем
	asort($arr);

	// последний
	$result = array_slice($arr, -1, 1);
	return $result;
}
?>