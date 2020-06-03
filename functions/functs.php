<?php
//Опшенсы простого селекта
function SelectOpts($query, $col_val, $col_name, $sel_val, $defoult)
{	$selected = '';
 if($defoult)
	echo '<option value="0">'.$defoult.'</option>';
	foreach($query as $q)
	{
		if($sel_val)
		{
		if($q[$col_val] == $sel_val)
			$selected = 'selected';
		}
		echo '<option value="'.$q[$col_val].'" '.$selected.'>'.$q[$col_name].'</option>';
		$selected = '';
	}
}

function LettersOnly($string)
	{
		$string = strip_tags($string);
		$string = preg_replace('/[^a-zA-Zа-яА-ЯёЁ ]/ui', '',$string);
		$string = trim($string);
		return($string);
	}

function DigitsOnly($string)
	{
		//echo $string;
		$string = strip_tags($string);
		$string = preg_replace('/[^0-9]/ui', '',$string);
		$string = intval($string);
		return($string);
	}

function ItemNames($string)
	{
		$string = strip_tags($string);
		$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ \,\.\(\)\]\[\_\:«»\-]/ui', '',$string);
		$string = trim($string);
		return($string);
	}

function Description($string)
{
	$string = str_replace('<hr class="hr_long">', '<br>',$string);
	$string = strip_tags($string,'<br>');
	$string = str_replace('123Ячейки для гравировки:', '',$string);
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ \,\.\(\)\]\[\_\:«»\-(?<br>)]/ui', '',$string);
	$string = trim($string);
	return($string);
}

function Comment($string)
{
	$string = strip_tags($string,'<br>');
	$string = preg_replace('/[^0-9a-zA-Zа-яА-ЯёЁ \,\.\(\)\]\[\_\:«»\-(?<br>)]/ui', '',$string);
	$string = trim($string);
	return($string);
}

function UserAucPrice($item_id,$user_id,$only = false,$friends = [])
{
	$friends = [3085];
	$server_group = ServerInfo($user_id);
	if(count($friends)>0)
	{
		$friendlist = $user_id.','.implode(',',$friends);
		$and = '`user_id` IN ('.$friendlist.')';
	}else
	{
		$and = "`user_id` = '$user_id'";
	}
	//var_dump($and);
	$query = qwe("SELECT * FROM `prices` WHERE 
	`item_id` = '$item_id' 
	AND ".$and." AND 
	`server_group` = '$server_group'
	order by `time` DESC LIMIT 1");
	if($query and mysqli_num_rows($query) > 0)
	{
		foreach($query as $q)
		{
			$auc_info['auc_price'] = $q['auc_price'];
			$auc_info['time'] = $q['time'];
			$auc_info['myprice'] = ($q['user_id'] == $user_id);
			$auc_info['isfriend'] = in_array($q['user_id'],$friends);
		}
		return $auc_info;
	}
	
	if($only) 
		return null;
	
	$query = qwe("SELECT * FROM `prices` WHERE 
	`item_id` = '$item_id' AND  
	`server_group` = '$server_group'
	order by `time` DESC LIMIT 1");
	if(mysqli_num_rows($query) > 0)
	{
		foreach($query as $q)
		{
			$auc_info['auc_price'] = $q['auc_price'];
			$auc_info['time'] = $q['time'];
			$auc_info['myprice'] = false;
			$auc_info['isfriend'] = false;
			$auc_info['price_autor'] = $q['user_id'];
		}
		return($auc_info);
	}
	else
	return(false);
}

function ServerInfo($user_id,$what = 'server_group')
{
	
	$defaults = ['server_group' => 2,'server' => 9];
	$query = qwe("SELECT `server`, `server_group` 
	FROM `user_servers`
	INNER JOIN `servers` 
	ON `user_servers`.`server` = `servers`.`id`
	AND `user_servers`.`user_id` = '$user_id'");
	$serv_info = mysqli_fetch_assoc($query);
	//var_dump($serv_info);
	$server_data = $serv_info[$what] ?? $defaults[$what];
	//var_dump($user_id);
	return $server_data;
}

function PriceInputs($auc_price,$item_id)
{
	$gol = strrev(substr(strrev($auc_price),4,10));
	$sil = strrev(substr(strrev($auc_price),2,2));
	$bro = strrev(substr(strrev($auc_price),0,2));
	$img_gold = '<img src="img/gold.png" width="15" height="15" alt="gold"/>';
	$img_silver = '<img src="img/silver.png" width="15" height="15" alt="gold"/>';
	$img_bronze = '<img src="img/bronze.png" width="15" height="15" alt="gold"/>';
	?>
	<div class="money-line">
	<input type="number" name="setgold[<?php echo $item_id?>]" value= "<?php echo $gol?>" id="gold_down" autocomplete="off"><?php echo $img_gold?></div>
	<div class="money-line">
	<input type="number" name="setsilver[<?php echo $item_id?>]" value= "<?php echo $sil?>" id="silbro_down" autocomplete="off" max="99"><?php echo $img_silver?></div>
	<div class="money-line">
	<input type="number" name="setbronze[<?php echo $item_id?>]" value= "<?php echo $bro?>" id="silbro_down" autocomplete="off" max="99"><?php echo $img_bronze?></div>
	<?php
}

function MoneyLineBL($auc_price,$item_id,$color = 'white',$is_show = 0)
{
	$is_show = $is_show ?? 0;
	$is_shows = ['style="display: none;"',''];
	$gol = strrev(substr(strrev($auc_price),4,10));
	$sil = strrev(substr(strrev($auc_price),2,2));
	$bro = strrev(substr(strrev($auc_price),0,2));
	$img_gold = '<img src="img/gold.png" width="15" height="15" alt="g"/>';
	$img_silver = '<img src="img/silver.png" width="15" height="15" alt="s"/>';
	$img_bronze = '<img src="img/bronze.png" width="15" height="15" alt="b"/>';
	?>
	
	<div class="money-line">
	<input type="number" name="setgold" class="pr_inputs" value= "<?php echo $gol;?>" min=0 max="999999999" id="gol_<?php echo $item_id;?>" autocomplete="off" <?php echo $color;?>><?php echo $img_gold;?></div>
	
	<div class="money-line">
	<input type="number" name="setsilver" class="pr_inputs" value= "<?php echo $sil;?>" min=0 max=99 id="sil_<?php echo $item_id;?>" autocomplete="off" <?php echo $color;?>><?php echo $img_silver;?></div>
		
	<div class="money-line">
	<input type="number" name="setbronze" class="pr_inputs" value= "<?php echo $bro;?>" min=0 max=99 id="bro_<?php echo $item_id;?>" autocomplete="off" <?php echo $color;?>><?php echo $img_bronze;?></div>
	<input type="hidden" name="item_id" value="<?php echo $item_id;?>">
	<input type="button" id="prdel_<?php echo $item_id;?>" <?php echo $is_shows[$is_show]?> name="del" class="small_del" value="del" data-tooltip="Удалить свою цену"></input>
	<?php
		
}

function esyprice($total,$size = 15,$only = false)
{
	$gold = '<img src="img/gold.png" width="'.$size.'" height="'.$size.'" alt="g"/>';
	$silver = '<img src="img/silver.png" width="'.$size.'" height="'.$size.'" alt="s"/>';
	$bronze = '<img src="img/bronze.png" width="'.$size.'" height="'.$size.'" alt="b"/>';
	$gol= strrev(substr(strrev($total),4,10));
	if($gol == 0) $gold = '';
		$sil = strrev(substr(strrev($total),2,2));
	if($sil == 0 and $gold == '') {$silver = ''; $rsil = '';};
		$bro = strrev(substr(strrev($total),0,2));
	if($only)
		return $gol.$gold.$sil.$silver.$bro.$bronze;
	
	return '<div class="esyprice">'.$gol.$gold.$sil.$silver.$bro.$bronze.'</div>';
}

function median($arr)
{ //Медиана от массива $arr
	if(!$arr) return false;
 sort ($arr);
 $count = count($arr);
 $middle = floor($count/2);
 if ($count%2) return round($arr[$middle],0);
 else return round(($arr[$middle-1]+$arr[$middle])/2,0);
}

function RepMedian($valut_id,$user_id = false)
{	
	global $server_group;
	$max = MaxValuta($valut_id);
	$qwe = qwe("SELECT
	`prices`.`user_id` as puser_id,
	(`user_id` = '$user_id') as `isuser`,
	`prices`.`item_id`,
	`prices`.`auc_price`,
	`items`.`price_buy`,
	`items`.`item_name`,
	round(`prices`.`auc_price`*0.9/`items`.`price_buy`,0) as `repcost`
	FROM
	`prices`
	INNER JOIN `items` 
	ON `prices`.`item_id` = `items`.`item_id` 
		AND `items`.`valut_id` = '$valut_id'
		AND `server_group` = '$server_group' 
		AND `prices`.`auc_price` > 1
		AND `items`.`personal` != 1
		AND `price_buy` < '$max'
	ORDER BY `item_id`, `isuser`");
	if(!$qwe or $qwe->num_rows == 0)
	return false;
	$last = 0;
	foreach($qwe as $r)
	{
		extract($r);
		if($isuser)
		{	
			$rprices[] = $repcost;
			$last = $item_id;
			continue;	
		}
		if($last == $item_id)
			continue;
		
		$rprices[] = $repcost;
		//$last = $item_id;
	}
	return median($rprices);
}

function ItemsFromAlterValutes($valut_id)
{
	$qwe = qwe("
	SELECT 
	`item_id`, 
	`item_name`, 
	`price_buy`, 
	`icon`,
	basic_grade
	FROM `items` 
	WHERE `personal` !=1 
	AND `valut_id` = '$valut_id'
	AND `is_trade_npc` = 1
	AND `on_off` = 1
	ORDER BY `categ_id`");
	if(mysqli_num_rows($qwe)>0)
	{
		?>
		<details class="for1" open><summary><b>Передаваемые предметы за эту валюту:</b></summary>
		<div class="up_craft_area">
		<?php
			Cubiki($qwe);
		?>
		</div></details>
		<?php
	}
	else 
	{
		?>
		<div class="for1"><b>Передаваемых предметов за эту валюту не найдено.</b></div>
		<?php
	}; 
};

function MaxValuta($valut_id)
{
	$maximums = [
	3 => 700,
	4 => 8000,
	6 => 501,
	23633 => 2000
];
	if(isset($maximums[$valut_id]))
		return $maximums[$valut_id];
	else 
		return 100000;
}

function MonetisationList($val_link, $valut_id, $user_id)
{

	
	$max = MaxValuta($valut_id);
	$array = $formedian = [];
	$qwe = qwe("
	SELECT 
	`item_id`, `item_name`, `price_buy`, `icon`, `basic_grade`
	FROM `items` 
	WHERE `personal` !=1 
	AND `valut_id` = '$valut_id'
	AND `is_trade_npc` = 1
	AND `on_off` = 1
	AND price_buy < '$max'
	ORDER BY `categ_id`");
	if(!$qwe or $qwe->num_rows == 0) 
		return false;
	
	foreach($qwe as $p)
	{
		$price_buy = $p['price_buy'];
		$item_id = $p['item_id'];
		$item_name = $p['item_name'];
		$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
		$basic_grade = $p['basic_grade'];
		if(!$auc_price) continue;

		$val_pr = round($auc_price/$price_buy*0.9,0);
		$array[$item_id.'_,_'.$item_name.'_,_'.$price_buy] = $val_pr;
		$icons[$item_id] = $p['icon'];
		$formedian[$item_id] = $val_pr;
	}
	arsort($array);
	ob_start();
	foreach ($array as $k => $v)
	{
		$arr = explode('_,_',$k);
		$item_id = $arr[0];
		$item_name = $arr[1];
		$price_buy = $arr[2];
		$icon = $icons[$item_id];
		$price = $v;

		?><div class="nicon">

		<?php
		Cubik($item_id,$icon,$basic_grade);
		?>
		<div class="itemname"><?php echo $item_name?>
		<?php echo '<div class="valut_median">'.$val_link.' = '. esyprice(round($price,0)).'</div>'; ?>
		</div>
		</div><?php
	}
	//printr($formedian);
	$median = median($formedian);
	$echodata = ob_get_contents();
	ob_end_clean();
	return [$median,$echodata];
}

function Cubik($item_id,$icon,$grade = 1,$tooltip = '',$dig_in=false)
{
	if(!empty($tooltip))
		$tooltip = 'data-tooltip="'.$tooltip.'"';
	if($dig_in !== false and !empty($dig_in))
		$dig_in = '<div class="matneed">'.$dig_in.'</div>';
	else
		$dig_in = '';
	
	?>
	<div class="itim" id="itim_<?php echo $item_id?>" style="background-image: url(/img/icons/50/<?php echo $icon?>.png)">
		<div class="grade" <?php echo $tooltip?> style="background-image: url(/img/grade/icon_grade<?php echo $grade?>.png)">
			<?php echo $dig_in?>
		</div>
	</div>
	<?php
}

function Comments($userinfo_arr,$item_id)
{
	extract($userinfo_arr);
	//printr($userinfo_arr);
?>
	<div class="comments">
		<h4>Комментарии</h4><hr>
<?php

$query = qwe("
SELECT 
`user_id`, 
`user_nick`, 
`avafile`, 
`mess`, 
`reports`.`time` 
FROM `reports`
INNER JOIN `mailusers` ON `user_id` = `mail_id`
WHERE `item_id` = '$item_id'");
if($query and mysqli_num_rows($query) > 0)
{
	foreach($query as $com)
	{
		$user_ava = $com['avafile'];
		$mess = htmlspecialchars($com['mess']);
		$mailnick = $com['user_nick'];
		$date = date('d.m.Y', strtotime($com['time']));
		$time = date('H:i', strtotime($com['time']));
	?>
	<div class="comments_row">	
		<div class="user_info">
			<div class="navicon" style="background-image: url(img/avatars/<?php echo $user_ava;?>);"></div>
			
			
		</div>
		<div class="comment">
		<div><h5><?php echo $mailnick;?></h5>
		<?php echo $mess;?></div>
		<div class="comdate"><?php echo $date.' '.$time;?></div>
		</div>
	</div>
	<div class="clear"></div>
	<br>
	<?php
	}
}
else
	echo 'Нет комментариев';
?>
	
	<div class="comments_row">
	

<?php

if($email)
{
?>
<div class="user_info">
	<a href="profile.php">
	<div class="navicon" style="background-image: url(<?php echo $avatar;?>);"></div>
	</a>
	</div>
	<div class="comment">
<form method="post" name="comment" action="comment.php">
<!--<p><label for="report_type2"> Подробно:</p>-->
<input type="hidden" name="item_id" value="<?php echo $item_id?>" autocomplete="off">
<textarea class="textarea" name="text" placeholder="Ваш комментарий"></textarea>
<button type="submit" class="def_button" id="comsend" value="Отправить" name="send">Отправить</button>
</form>
</div>
<?php
}else 
{
	?><a href="oauth/mailru.php?path=catalog&item_id=<?php echo $item_id?>" style="color: #6C3F00; text-decoration: none;"><b>Войдите</b></a>, чтобы оставлять комментарии<?
}

?>


</div>
<?php
}

function ServerSelect()
{
	global $server;
	$query = qwe("
	SELECT * 
	FROM `servers` 
	ORDER BY 
		server_group, server_name
	");
	/*
	$i = $old = 0;
	ob_start();
	foreach($query as $q)
	{$i++;
	 	
	 	extract($q);
	 	
	 	if($server_group != $old)
		{
			if($i > 1)
				echo '</ul>';
			
			echo '<ul>';
		}
			
	 
		echo '<li>'.$server_name.'</li>';
	
		$old = $server_group;
	}
	echo '</ul>';
	$slist = ob_get_contents();
	ob_end_clean();
	$tooltip = 'Аукционные группы:<br>'.$slist;
	$tooltip = htmlspecialchars($tooltip);
	*/
	$tooltip = '';
	?>
	<form method="POST" data-tooltip="<?php echo $tooltip?>" action="serverchange.php" name="server">
		<select name="serv" id="server" class="server" onchange="this.form.submit()">
		<?php
		
		SelectOpts($query, 'id', 'server_name', $server, false);	
		?>
		</select>
	</form>
	<?php
}

function SetToken()
{
	global $user_id;
	if(!isset($user_id))
		return false;
	
	$token = random_str(12);
	$qwe = qwe("
	UPDATE `mailusers`
	SET `token` = '$token',
	`last_time` = NOW()
	WHERE `mail_id` = '$user_id'
	");
	if($qwe)
		return $token;
}

function AskToken()
{
	global $user_id;
	if(!isset($user_id))
		return false;
	
	$qwe= qwe("
	SELECT `token` 
	FROM `mailusers`
	WHERE 
	`mail_id` = '$user_id'
	AND `last_time` > (NOW() - INTERVAL 60 MINUTE)
	AND LENGTH(`token`) > 0
	");
	if(!$qwe or $qwe->num_rows == 0) 
		return false;
	$qwe = mysqli_fetch_assoc($qwe);
	return $qwe['token'];
}

function SPM_array()
{
	$qwe = qwe("
	SELECT
	craft_materials.craft_id,
	ROUND(SQRT(crafts.mins*SQRT(seeds.cultisize)/crafts.result_amount)) as spm
	FROM
	crafts
	LEFT JOIN craft_materials ON craft_materials.craft_id = crafts.craft_id
	AND craft_materials.mater_need > 0 
	AND dood_id = 10
	AND crafts.on_off
	inner JOIN seeds ON seeds.item_id = craft_materials.item_id
	inner JOIN items ON craft_materials.item_id = items.item_id 
	AND items.on_off
	");
	if(!$qwe or $qwe->num_rows == 0) 
		return false;
	foreach($qwe as $q)
	{
		$spms[$q['craft_id']] = $q['spm'];
	}
	
	$qwe = qwe("
	SELECT
	crafts.craft_id,
	ROUND(SQRT(crafts.mins*SQRT(1200)/crafts.result_amount)) as spm
	FROM
	crafts
	WHERE dood_id = 9108
	AND crafts.on_off
	");
	if(!$qwe or $qwe->num_rows == 0) 
		return false;
	foreach($qwe as $q)
	{
		$spms[$q['craft_id']] = $q['spm'];
	}
	return $spms;
}
?>