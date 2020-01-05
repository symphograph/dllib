<?php

function orrepcost($user_id, $server_group)
{
	global $orcost, $repprice, $honorprice, $dzprice, $soverprice;
	$specialq = qwe("SELECT * from `prices` where `item_id` in (2, 3, 4,5,6, 23633) 
	AND `server_group` = '$server_group' 
	AND `user_id` = '$user_id' 
	ORDER BY `time` DESC ");
	$orcost = $repprice = $honorprice = $dzprice = $soverprice = false;
	foreach($specialq as $skey)
		{
			if($skey['item_id'] == 2) $orcost = $skey['auc_price'];
			if($skey['item_id'] == 3) $repprice = $skey['auc_price'];
			if($skey['item_id'] == 4) $honorprice = $skey['auc_price'];
			if($skey['item_id'] == 23633) $dzprice = $skey['auc_price'];
		} 
//var_dump($repprice);
	if(!$orcost) $orcost = 1;
	if(!$repprice) $repprice = RepMedian($server_group,'Ремесленная репутация');
	if(!$honorprice) $honorprice = RepMedian($server_group,'Честь');
	
	$orrepcost[2] = $orcost;
	$orrepcost[3] = $repprice;
	$orrepcost[4] = $honorprice;
	$orrepcost[23633] = $dzprice;
	return $orrepcost;
}

function auc_price($itemq, $item_id, $auc_price, $spec_price, $myprice, $user_id)
{
	//var_dump($user_id);
	global $HardPersonal;
	
	$auc_price_info = PriceMode($item_id,$user_id)['auc_price'];
	
	$myprice = $auc_price_info['user_id'] == $user_id;
	if((!$spec_price) or in_array($item_id, $HardPersonal))
	$auc_price = $auc_price_info['auc_price'];
	$time = $auc_price_info['time'];
	if($myprice) 
		$who = 'вы видели'; 
		else 
		$who = 'кто-то видел';

	if($time > 0)
	$time = date('d.m.Y - H:i', strtotime($time)); else $time = 'Последний раз';
	
	echo '<div class="looked"><div class="lookedin"><hr>';
	//Для итемов, которым обычный расчет не подходит
	if($spec_price or in_array($item_id, $HardPersonal))
	{
		 
		if(in_array($item_id, $HardPersonal)) 
			$mess = '<br>Будем считать, что это стоит:<br>';
		else 
			$mess = '<br>Можно продать NPC за:<br>';

	} 
	else
		$mess = '<br>'.$time.'<br> на аукционе '.$who.' по:<br>';
	
	echo $mess;
	?>
	<form action="edit/setprice.php" method="POST">
	<div class="money_area_down">
	<?php
	MoneyLine($auc_price);
	
	if((!$spec_price) or in_array($item_id, $HardPersonal))
	{
		?>
		<div style="margin-top: 5px; display: inline-block;"></div>
		<br><br>
		<input type="submit" class="crft_button" name="sendprice" value="Сохранить">
		<?php
	}
	?>
	</div>
	<div class="money-line"><input type="hidden" name="setname" id="search_box" 
	value="<?php echo $itemq?>" autocomplete="off" display="none">
	<input type="hidden" name="item_id" id="search_box" 
	value="<?php echo $item_id?>" autocomplete="off" display="none"></div>
	</form></div></div>
	<?php
};

function MoneyLine($auc_price,$item_id)
{
	$gol = strrev(substr(strrev($auc_price),4,10));
	$sil = strrev(substr(strrev($auc_price),2,2));
	$bro = strrev(substr(strrev($auc_price),0,2));
	$img_gold = '<img src="img/gold.png" width="15" height="15" alt="gold"/>';
	$img_silver = '<img src="img/silver.png" width="15" height="15" alt="gold"/>';
	$img_bronze = '<img src="img/bronze.png" width="15" height="15" alt="gold"/>';
	?>
	<div class="money-line">
	<input type="number" name="setgold" value= "<?php echo $gol;?>" id="gold_down" autocomplete="off"><?php echo $img_gold;?></div>
	<div class="money-line">
	<input type="number" name="setsilver" value= "<?php echo $sil;?>" id="silbro_down" autocomplete="off" max="99"><?php echo $img_silver;?></div>
	<div class="money-line">
	<input type="number" name="setbronze" value= "<?php echo $bro;?>" id="silbro_down" autocomplete="off" max="99"><?php echo $img_bronze;?></div>
	<?php
}

function parent_recs_ecco($item_id, $hrefself, $dbLink)
{
	$queryhi = qwe("
	SELECT DISTINCT 
	`items`.`item_name`, 
	`crafts`.`result_item_id`, 
	concat(items.icon,'.png') as icon 
	FROM `crafts`
INNER JOIN `items` ON items.item_id = `crafts`.`result_item_id` AND `items`.`on_off` = 1
	WHERE `crafts`.`on_off` = 1
	AND `craft_id` in
	(SELECT DISTINCT `craft_materials`.`craft_id`
	FROM `craft_materials`
	WHERE `craft_materials`.`item_id` = '$item_id'
	AND `craft_materials`.`mater_need` > 0)
	AND `result_item_id` in
	(select `item_id` FROM `items`
	WHERE `on_off` = 1
	AND `item_name` is NOT NULL 
	AND `item_name` != '')
	ORDER BY `deep` DESC, `result_item_id`");
	if (mysqli_num_rows($queryhi)>0)
	{
		echo '<details class="for1" open="open"><summary><b>Используется в рецептах:</b></summary><div class="hirec">';
		while ($array = mysqli_fetch_assoc($queryhi))
		{
			$result_item_name =$array['item_name'];
			$result_item_id =$array['result_item_id'];
			//$item_link= str_replace(" ","+",$result_item_name); 
			echo '<a href="'.$hrefself.'?query_id='.$result_item_id.'" title="'.$result_item_name.'"  data-toggle="tooltip" data-placement="top">';
			?>
			<div class="itemline-hi"> <div class="itim" style="background-image: url(img/icons/50/<?php echo $array['icon'];?>)"></div>
			</div></a>
			<?php
		}
		echo '</div></details>';
	}
	else 
	{
		echo '<div class="for1"><b>Не используется в рецептах.</b></div>';
	}; 
};

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
				$best = BestCraftForItem($user_id,$mat_id);
				
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
			$best = BestCraftForItem($user_id,$mat_id);

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

//Для калькулятора
function child_recs_ecco($item_id, $hrefself, $dbLink, $orcost, $matrow, $price_q, $user_id, $auc_price, $forchenge, $itemq, $personal, $prof_q, $crft_nalog, $pump, $grade_color,$u_amount)
{
	global $matrow, $total, $prof, $lvl, $crft_nalog, $mat_deep, $prof_id, $used, $best_prof_id, $best_used, $best_prof_name, $best_lvl;
	$lvlranges = array('0', '0', '5', '10', '15', '20', '20', '20', '20', '25', '30', '40'); $lvl = 0;
	$matrow = ''; $show_or_add = false;
	?>
	<form method="post" action="">

		<p><div class="rec_td2">
		<div class="itemprompt" data-title="Тогда я выберу рецепт автоматически">
		<input type="submit" class="crft_button" value="Забудь мой выбор" name="unset_u_craft"/></div></div>
		<div class="rec_td2"><div class="itemprompt" data-title="Я забуду ВСЕ рецепты, которые ты предпочитал и выберу какие мне хочется.">
		<input type="submit" value="Забудь все" class="crft_button" name="unset_all_craft"/></div></div></p><br>
		<p>Рецепты получения:</p>
		<label for="u_amount">Количество: </label>
		<input class="result_amount" type="number" id="u_amount" name="u_amount" min="1" value="<?php echo $u_amount;?>" onchange="this.form.submit()"/>шт
		
	<?php
	$query = qwe("SELECT
	`crafts`.`craft_id`,
	`crafts`.`dood_id`,
	`crafts`.`dood_name`,
	`crafts`.`result_item_id`,
	`crafts`.`result_item_name`,
	`crafts`.`labor_need`,
	round(`labor_need` * (100 - IFNULL(`save_or`,0)) / 100,0) AS `labor_need2`,
	`crafts`.`result_amount`,
	`crafts`.`craft_time`,
	`crafts`.`prof_id`,
	`user_crafts`.`craft_price`,
	`user_crafts`.`auc_price`,
	`user_crafts`.`isbest`,
	`user_crafts`.`labor_total`,
	`crafts`.`rec_name`,
	`user_profs`.`lvl`,
	`prof_lvls`.`min`,
	`prof_lvls`.`max`,
	`prof_lvls`.`save_or`,
	`prof_lvls`.`save_time`,
	`profs`.`profession`,
	`profs`.`used`,
	`items`.`icon`
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
	INNER JOIN `items` ON `items`.`item_id` = '$item_id'
	ORDER BY `isbest` DESC, `craft_price`");
	$bye = false;
	$bye_vs_craft = 0;
	include 'includs/ip.php';
	 $user_cr = false;
	$i = 0;
	$best_used = $best_prof_id = 0;
	$best_prof_name = '';
	foreach($query as $key)
	{
		$i++;
		if($pump and $i == 2) break;
		if($i == 2)
			echo '<details class="details"><summary><b>Смотреть другие рецепты</b></summary>';
		if($key['auc_price'] > 0) 
			$bye = true;
		$best = ($key['isbest'] > 0);
		$craft_id = $key['craft_id'];
		$cost = $key['craft_price'];
		if($key['isbest']==1)
        	$isbest_mes = '<div class="sor"><b>Предлагаю этот рецепт</b>';
		if($key['isbest'] == 2)
			$isbest_mes = '<div class="sor"><b>Ты выбрал этот рецепт</b>';
		if($key['isbest'] <1 or $key['isbest'] ==3)
			$isbest_mes = '<div class="sor"><input type="radio" name="chenge_best" value="'.$craft_id.'" onchange="this.form.submit()"> <b>Выбрать этот.</b>';
		if($key['isbest'] >1) 
			$user_cr = true;
		$used = $key['used'];
		if($used > 0)
		$or = $key['labor_need2'];
		else 
		$or = $key['labor_need'];
		$all_or = $key['labor_total'];
		$profession = $key['profession'];
		$prof_id = $key['prof_id'];
		$lvl = $key['lvl'];
		$result_amount = $key['result_amount'];
		if($best and $used > 0)
		{
			$best_prof_id = $prof_id;
			$best_used = $used;
			$best_prof_name = $profession;
			$best_lvl = $lvl;
		}
		
		if($profession !== '') 
			$prof = $profession;
		$profimg = $prof_img= '<img src="img/profs/'.str_replace(" ","_",$profession).'.png" width="20" height="20" alt="'.$profession.'" title="'.$profession.'"> ';
		$dood = '<br>'.$key['dood_name'].'<br><hr>';
		if(empty($key['dood_name'])) 
			$dood = '<br><br><hr>'; 
		?>
		<p><div class="rec_row">
		<div class="rec_td1">
		<div class="itemprompt" data-title="<?php echo $itemq;?>">
			<div class="top_itim" style="background-image: url(img/icons/50/<?php echo $key['icon']?>.png); border-color: <?php echo $grade_color;?>;">
				<div class="itdigit"><?php echo $result_amount*$u_amount;?></div>
			</div>
		</div>
		<div><?php echo $profimg.' ОР: '.$or*$u_amount.$dood;?></div><br>
		
		<div>ОР на всю цепочку: <?php echo $all_or*$u_amount;?> (на <?php echo $u_amount;?> шт)</div><br>
		<b>Итого на <?php echo $u_amount;?> шт: </b>
		<?php
		last_alter_crafts($key, $dbLink, $craft_id, $orcost, $user_id, $or, $u_amount);
		echo esyprice($cost*$u_amount);
		if($auc_price > 0)
		{
			$pribil = round($auc_price*0.9 - $cost)*$u_amount;
			if($or > 0)	
				$pribilor = round($pribil/$all_or/$u_amount);
			echo '<br><hr><div class="itemprompt" data-title="10% отдали аукциону"><p>Прибыль с '.$u_amount.' шт: ';
			echo esyprice($pribil);
			echo '</p></div>';
			if($or > 0)
			{
				echo '<div class="sor"><p>Прибыль с 1 ОР: ';
				echo esyprice($pribilor);
				echo '</div>';
			}
		}
		if($best) $bye_vs_craft = $auc_price-$cost;
		if($bye_vs_craft < 0)$b_v_cr = " Рекомендуется купить.";
		if($bye_vs_craft > 0)$b_v_cr = " Рекомендуется крафтить.";
		//echo '<br>прибыль: '.$auc_price-$cost.'<br>';
		echo '</p>'.$isbest_mes.'</div></div><div class="rec_td2"><div class="matcontent">';
		if($matrow)
        	echo implode('',$matrow); $matrow = array();
		echo '</div>';
		if($crft_nalog > 0)
		{
			echo '<div class="crft_nalog">Просит денег для крафта: ';
			echo esyprice($crft_nalog*$u_amount);
			echo'</div>';
		}	
		echo '</div></div></p>';
		if($myip){echo '<a href="edit/recedit.php?query='.$craft_id.'" target="_blank">Править</a><br>
		<a href="edit/recedit.php?off='.$craft_id.'">Отключить</a>';};
		//echo '<hr class="hr">';
		if($best)
		//Если рецепт основной, показываем полный список ресов	
		{		
			qwe("DELETE FROM `craft_all_mats` WHERE `user_id` = '$user_id'");
			$mats = array();
			all_res($user_id, $item_id, 1, $mat_deep);
			
			if($mat_deep > 1)
				include 'all_res.php';
		}
	   
	};
	
	if($i > 1)
	echo '</details><br>';
	
		
	if(!$bye and $personal < 1 and !$pump)
	{
		?>
		<p><div class="rec_row rec_row2">
		<div class="rec_td1"></div><br>
		<input type="radio" name="chenge_best" value="bye" onchange="this.form.submit()"> Не буду крафтить. Куплю.	
		<?php
		echo ''.$forchenge;
	}
  
  if($bye)  echo '<p><div class="rec_row rec_row2"><div class="rec_td1"></div><br><b>Это мы покупаем.</b>';
  if($personal < 1 and $auc_price > 0) echo '<br>'.$b_v_cr;

?>
<input type="hidden" name="bye_price" value="<?php echo $auc_price?>">
<input type="hidden" name="bye_itemid" value="<?php echo $item_id;?>">
	</div><div class="rec_td2"></div></p>
	</form><?php
	//echo '<hr>';
	
};

//Вывод ПОСЧИТАННЫХ материалов рецепта и их суммарной стоимости.
function last_alter_crafts($key, $dbLink, $craft_id, $orcost, $user_id, $or, $u_amount)
{	
	global $matrow, $total, $crft_nalog;
	$hrefself = $_SERVER['PHP_SELF'];
	$itog = $key['result_amount'];
	$res_name = $key['result_item_name'];
	$item_id = $key['result_item_id'];
	$rec_name = $key['rec_name'];
	$or_need = $or;
	$profession = $key['profession'];
	$dood = $key['dood_name'];
	//$next = $key['next'];
	if($key['dood_name'] == 'Лаборатория')
	$itog = $itog*1.1;
	$query2 = qwe("
	SELECT 
	`craft_materials`.`mater_need`, 
	`items`.`item_name`, 
	`items`.`price_buy`, 
	`items`.`price_sale`, 
	`items`.`price_type`, 
	`items`.`is_trade_npc`, 
	`items`.`item_id`, 
	`items`.`craftable`,
	`craft_materials`.`mat_grade`,
	`items`.`icon`,
	user_crafts.craft_price
	FROM `craft_materials`
	INNER JOIN `items` ON `items`.`ismat` = 1 AND 
	`craft_materials`.`craft_id` = '$craft_id' AND 
	`items`.`item_id` = `craft_materials`.`item_id` AND 
	`items`.`on_off` = '1'
	LEFT JOIN user_crafts ON user_crafts.craft_id = `craft_materials`.`craft_id` AND user_crafts.user_id = '$user_id'
	ORDER BY `item_id`");
	$sum = 0; $matrow = array(); $crft_nalog = 0;
	foreach($query2 as $ikey)
		{
			$price_type = $ikey['price_type'];
			if($price_type == '') $price_type ='gold';
			$mater_need = $ikey['mater_need'];
			$matname = $ikey['item_name'];
			$npc = ($ikey['is_trade_npc'] > 0);
			$craftable = ($ikey['craftable'] > 0);
			$pr = $ikey['price_buy'];
			$mat_grade = $ikey['mat_grade'];
			//if($next == 1 and $mater_need < 0) $pr = $pr*0.9;
			$mater = $ikey['item_id'];
			$total = $ikey['craft_price'];

			if($npc)
			{$pr = $ikey['price_buy'];};
			if(!$npc and $craftable)
			{

				$q_craft_pr = qwe("
				SELECT `craft_price`, `auc_price`  
				FROM `user_crafts` 
				WHERE  `isbest` > 0 
				AND `item_id` = '$mater' 
				AND `user_id` ='$user_id'");
				//$cnt = mysqli_num_rows($q_craft_pr);
				$arrbest = mysqli_fetch_assoc($q_craft_pr);
				$pr = $arrbest['craft_price'];
				if($arrbest['auc_price']>0) $pr = $arrbest['auc_price'];
			}
			if((!$npc and !$craftable and $mater != '500') or $mater_need<0)
			{
				

				
				$user_aucprice = PriceMode($mater,$user_id);
				if($user_aucprice)
				{
					$pr = $user_aucprice['auc_price'];
					$price_type = 'gold';
				}
				else $pr = 0;
				
			}
			if(in_array($price_type,['Ремесленная репутация','Честь','Очки вклада','Дельфийская звезда']))
			{
				$user_aucprice = PriceMode($mater,$user_id);
				if($user_aucprice)
				{
					$pr = $user_aucprice['auc_price'];
					$price_type = 'gold';
				}else
				{
					$server_group = ServerInfo($user_id);
					$pr = RepMedian($server_group, $price_type, $user_id);
					$pr = $pr*$ikey['price_buy'];
					$price_type = 'gold';
				}
			}
			$pr = intval($pr);
				$matsum = $mater_need*$pr;

			$sum = $sum+$matsum;
			
			if($mater == 500)
			$crft_nalog = $mater_need;
             $prs = $pr;
			if(isset($pr) and $price_type == 'gold') 
			$prs = round($pr/10000,4);
			
			if($pr == 0) $prs = '?';
			if($mater !== '500')
				{
					$grade_color = '#FFFFFF';
					$grade_name = '';
					if($mat_grade > 0)
					{
						$grade_arr = GradeInfo($mater,$mat_grade);
						$grade_color = $grade_arr[0];
						if($mat_grade > 1)
						$grade_name = ' ('.$grade_arr[1].')';
					}
					$item_link= str_replace(" ","+",$ikey['item_name']); 
					$matrow[] = '<a href="'.$hrefself.'?query_id='.$mater.'">
					<div class="itemline"><div class="itemprompt" data-title="'.$matname.$grade_name.' по '.$prs.$price_type.'"> 
					<div class="itim" style="background-image: url(img/grade/icon_grade'.$mat_grade.'.png), url(img/icons/50/'.$ikey['icon'].'.png); border-color: '.$grade_color.';">
					<div class="itdigit">'.$mater_need*$u_amount.'</div></div></div></div>
					</a>';
				};
		};

	//$total = $sum+($or_need*$orcost);
	//$total = round($total/$itog)*$u_amount;

};

function GradeInfo($item_id,$basic_grade)
{
	if($basic_grade < 1) 
		$basic_grade = 1;
	
	$query_basic_grade = qwe("SELECT * FROM `grades` WHERE `id` = '$basic_grade'");
	
	foreach($query_basic_grade as $qbg)
	{
		$grade_arr[0] = $qbg['color'];
		$grade_arr[1] = $qbg['gr_name'];
		$grade_arr[2] = $qbg['chance_craft'];
		return($grade_arr);
	}
}

///Для выяснения всех необходимых итемов для расчета всего дерева.
function res($item_id, $craftsq, $x, $crafta, $icrft, $crdeep, $crftorder)
{
	global $crafts, $crdeep, $deeptmp, $craftsq, $icrft, $crftorder, $user_id;
	$cr =0;
	$deeptmp= $deeptmp+1;
	if($craftsq)
	foreach($craftsq as $v)
	{
		
		$craft = $v['craft_id'];
		
		if($_SERVER['SCRIPT_NAME'] == '/hendlers/packs_list.php')
		{
			$qwe = qwe("
			SELECT `craft_id` 
			FROM `user_crafts` 
			WHERE `craft_id` = '$craft'
			AND user_id = '$user_id'
			");
			if($qwe->num_rows > 0) continue;
		}
		
		$mat_name = $v['item_name'];
		$mater_need =  $v['mater_need'];
		$mat_id = $v['item_id'];

		//echo 'Рецепт '.$x; $x++;
		if (in_array($mat_id, $crdeep) or $mater_need<0) continue;
		 $craftsq = qwe("
		 SELECT 
		 `item_name`, 
		 `mater_need`, 
		 `item_id`, 
		 `craft_id`, 
		 `result_item_name`, 
		 `result_item_id` 
		 FROM `craft_materials` 
		 WHERE `result_item_id` = '$mat_id'");
		
		 $need = $v['mater_need'];
		 $needs[] = $need;
		 //echo $v['item_name'].'<br>';
		 $crafts[] = $craft;
		 $crdeep[$mat_id] =  $mat_id;//Ключ нужен просто для уникального массива.
		 $count_crafts = mysqli_num_rows($craftsq);
		 $cr++;
		if($count_crafts > 0 and $need > 0)
		{
			$crftorder[] = $mat_id;
			$icrft++;
			res($item_id, $craftsq, $x, $crafta, $icrft, $crdeep, $crftorder);
		}
		$crafta = $craft;
	};
	$crdeep[$item_id] =  $item_id;//Ключ нужен просто для уникального массива.
	//return($crdeep);
};


?>