<?php
//Опшенсы простого селекта
function SelectOpts($query, $col_val, $col_name, $sel_val = false, $default = false)
{	$selected = '';
     if($default)
        echo '<option value="0">'.$default.'</option>';

    foreach($query as $q)
    {
        if($sel_val){
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

function esyprice($total,$only = false)
{

	$gold = '<img src="img/gold.png" style="width: 0.9em; height: 0.9em" alt="g"/>';
	$silver = '<img src="img/silver.png" style="width: 0.9em; height: 0.9em" alt="s"/>';
	$bronze = '<img src="img/bronze.png" style="width: 0.9em; height: 0.9em" alt="b"/>';
	$coins = [$bronze,$silver,$gold];
    $array  = str_split($total);
    krsort($array);
    $i = 0;
    $res = [];

    foreach ($array as $v){
        if($i % 2 === 0 and $i/2 < 3 and $v != '-'){
            $res[] = $coins[$i/2];
        }

        $res[] = $v;
        $i++;
    }
    krsort($res);

	$string = implode('',$res);

	if($only)
		return $string;
	
	return '<div class="esyprice">'.$string.'</div>';
}

function median(array $arr)
{
    $count = count($arr);
    if(!$count){
        return false;
    }

    sort ($arr);
    $middle = floor($count/2);
    if ($count%2) return round($arr[$middle],0);
    else return round(($arr[$middle-1]+$arr[$middle])/2,0);
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

function MonetisationList($val_link, int $valut_id)
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
		$Price = new Price($item_id);
		$Price->byMode();
		$auc_price = $Price->price;
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
		$icon = $icons[$item_id];
		$price = $v;

		?><div class="nicon">

		<?php
		$Cubik = new Cubik($item_id,$icon,$basic_grade);
		$Cubik->print();
		?>
		<div class="itemname"><?php echo $item_name?>
		<?php echo '<div class="valut_median">'.$val_link.' = '. esyprice(round($price,0)).'</div>'; ?>
		</div>
		</div><?php
	}

	$median = median($formedian);
	$echodata = ob_get_contents();
	ob_end_clean();
	return [$median,$echodata];
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

function SetToken()
{
	global $User;
	if(!isset($User->id))
		return false;
	
	$token = random_str(12);
	$qwe = qwe("
	UPDATE `mailusers`
	SET `token` = '$token',
	`last_time` = NOW()
	WHERE `mail_id` = '$User->id'
	");
	if($qwe)
		return $token;
}

function AskToken()
{
	global $User;
	if(!isset($User->id))
		return false;
	
	$qwe= qwe("
	SELECT `token` 
	FROM `mailusers`
	WHERE 
	`mail_id` = '$User->id'
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

function addScript(string $file)
{
    $ver = md5_file($_SERVER['DOCUMENT_ROOT'].'/'.$file);
    $path = $file . '?ver=' . $ver;
    ?><script type="text/javascript" src="<?php echo $path?>"></script><?php
}

function CssMeta(array $css_arr)
{
    $root = $_SERVER['DOCUMENT_ROOT'];
    foreach ($css_arr as $css)
    {
        ?><link href="/css/<?php echo $css?>?ver=<?php echo md5_file($root.'/css/'.$css)?>" rel="stylesheet"><?php
    }
}

function jsFile($file)
{
    ?><script type="text/javascript" src="<?php echo 'js/'.$file.'?ver='.md5_file($_SERVER['DOCUMENT_ROOT'].'/js/'.$file)?>"></script><?php
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
	return array_slice($arr, -1, 1);
}

function cmp($a, $b)
{
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

function sqlUpdateArgsToString(array $args){
    global $dbLink;
    if(!isset($dbLink))
        dbconnect();

    $arr = [];
    foreach ($args as $ak => $av){
        if(!is_int($av)){
            $av = mysqli_real_escape_string($dbLink,$av);
        }
        $arr[] = "`$ak` = '$av'";
    }
    return implode(', ',$arr);
}

function removeBOM($str="") : string
{
    if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
        $str = substr($str, 3);
    }
    return $str;
}
?>