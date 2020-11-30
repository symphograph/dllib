<?php
//Опшенсы простого селекта
function SelectOpts($query, $col_val, $col_name, $sel_val = false, $default = false)
{	$selected = '';
 if($default)
	echo '<option value="0">'.$default.'</option>';
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

function ServerInfo($user_id,$what = 'server_group')
{
	
	$defaults = ['server_group' => 2,'server' => 9,'server_name' => 'Сервер ?'];
	//Занятно. Я изобрел класс.=)
	$query = qwe("SELECT `server`, `server_group`, server_name 
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
	$gold = '<img src="img/gold.png" style="width: 0.9em; height: 0.9em" alt="g"/>';
	$silver = '<img src="img/silver.png" style="width: 0.9em; height: 0.9em" alt="s"/>';
	$bronze = '<img src="img/bronze.png" style="width: 0.9em; height: 0.9em" alt="b"/>';
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
	3 => 12001,
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
	if($dig_in and !empty($dig_in))
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

function Comments($User,$item_id)
{

?>
	<div class="comments">
		<h4>Комментарии</h4><hr>
<?php

$query = qwe("
SELECT 
`user_id`, 
`user_nick`, 
`avatar` as remote_avalink,
`avafile`, 
`mess`, 
identy as midenty,
`reports`.`time` 
FROM `reports`
INNER JOIN `mailusers` ON `user_id` = `mail_id`
WHERE `item_id` = '$item_id'");
if($query and mysqli_num_rows($query) > 0)
{
	foreach($query as $com)
	{
		$avafile = $com['avafile'];

		if($avafile and file_exists($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$avafile))
		    $puser_ava = $avafile;
	    else
            $puser_ava = AvaGetAndPut($com['remote_avalink'],$com['midenty']);

		$mess = htmlspecialchars($com['mess']);
		$mailnick = $com['user_nick'];
		$date = date('d.m.Y', strtotime($com['time']));
		$time = date('H:i', strtotime($com['time']));
	?>
	<div class="comments_row">	
		<div class="user_info">
			<div class="navicon" style="background-image: url(img/avatars/<?php echo $puser_ava;?>);"></div>
			
			
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

if($User->email)
{
?>
<div class="user_info">
	<a href="profile.php">
	<div class="navicon" style="background-image: url(<?php echo $User->avatar;?>);"></div>
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

function secToArray($secs)
{
	$res = [];

	$res['days'] = floor($secs / 86400);
	$secs = $secs % 86400;

	$res['hours'] = floor($secs / 3600);
	$secs = $secs % 3600;

	$res['minutes'] = floor($secs / 60);
	$res['secs'] = $secs % 60;

	return $res;
}

function FreshTimeSelect($item_id = false, $from_id = false)
{
    $per = '';
    if($item_id)
    {
        $item_id = intval($item_id);
        $from_id = intval($from_id);
        $qwe = qwe("
        SELECT 
        fresh_data.fresh_tstart,
        fresh_data.fresh_per,
        fresh_data.fresh_lvl,
        fresh_data.fresh_group,
        fresh_data.fresh_type
        FROM
        packs
        INNER JOIN pack_prices ON pack_prices.item_id= packs.item_id AND packs.item_id = '$item_id'
        INNER JOIN zones ON zones.zone_id = pack_prices.zone_id AND pack_prices.zone_id = '$from_id'
        INNER JOIN pack_types ON packs.pack_t_id = pack_types.pack_t_id AND pack_types.pack_t_id != 6
        INNER JOIN fresh_data ON pack_types.fresh_group = fresh_data.fresh_group  
        AND zones.fresh_type = fresh_data.fresh_type
        GROUP BY fresh_data.fresh_tstart");

    }else
    {
       $qwe = qwe("
        Select fresh_tstart from fresh_data
        GROUP BY fresh_tstart;
        ");
    }


    foreach($qwe as $i)
    {
        $time = $i['fresh_tstart'];

        if(($time/60) >= 24)
            $format = "jд. H:i";
        else
            $format = "H:i";

        $pack_time = date($format,$time*60-3600*3-3600*24);

        if($item_id)
            $per = ' '.$i['fresh_per'].'%';

        ?><option value="<?php echo $time?>"><?php echo $pack_time.$per;?></option><?php
    }
}

function is_image($filename) {
	$img_types = ['','gif','jpeg','png','swf','psd','bmp','tiff','tiff'];
  $is = @getimagesize($filename);
	//var_dump(filesize($filename));

  if ( !$is )
	  return false;
  if( !in_array($is[2], array(1,2,3)) )
	  return false;

  return $img_types[$is[2]];
}

function SearchWrapVariant(object $data)
{
    $item_name = htmlspecialchars($data->item_name, ENT_QUOTES, 'UTF-8');
	$item_id = $data->item_id;
	$icon = $data->icon;
	$personal = intval($data->personal);
    $craftable = intval($data->craftable);


    ob_start();
    $personals = ['','Персональный'];
    $crafts = ['','Крафтабельный'];
    ?>
    <div class="advice_variant" id="<?php echo $item_id?>" data-id="<?php echo $item_id?>">
        <img id="icon" width="40px" height="40px" src="img/icons/50/<?php echo $icon?>.png">
        <div>
            <div class="saw_name"><?php echo $item_name?></div>
            <div class="saw_notes"><?php echo $personals[$personal]?></div>
            <div class="saw_notes"><?php echo $crafts[$craftable]?></div>
        </div>
    </div>
    <hr>
    <?php
    return trim(one_line(ob_get_clean()));

}

function one_line($buffer)
{
// удалить пробелы между html тегами, кроме <pre>
	$buffer = preg_replace('/(?:(?<=\>)|(?<=\/\>))\s+(?=\<\/?)/', '', $buffer);
	/*
	if (FALSE === strpos($buffer, '<pre')) {
 $buffer = preg_replace('/\s+/', ' ', $buffer);
	}
	*/
// удалить новые строки,за которыми пробелы
	//$buffer = preg_replace('/[\t\r]\s+/', ' ', $buffer);
	return $buffer;
}
?>