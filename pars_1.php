<meta charset="utf-8">
<?php
include 'includs/ip.php';
if(!$myip) exit();
echo '<form method="post" action="">
<input type="submit" name="go" value="go">	
</form>';
if(!isset($_POST['go'])) exit();
include 'includs/config.php';
function curl_npc($plink)
{	
	$curl = curl_init();
	 curl_setopt($curl, CURLOPT_HEADER, 0);
	 curl_setopt($curl, CURLOPT_FAILONERROR, 1);
	 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // allow redirects
	 curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // return into a variable 
	 curl_setopt($curl, CURLOPT_URL, $plink);
	 curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	$somepage = curl_exec($curl);
	curl_close($curl);
	return $somepage;
}
//$id = 29; 
$n = 0; $drgr = 0;
$query = qwe("SELECT * FROM `npc` where `type` = 2");
foreach($query as $m)	
//for($id = 8000001;$id <= 8000092;$id++)
{
	$id = $m['npc_id'];
$str = $lut = $arr = array();
unset($somepage);
$plink = 'http://archeagedatabase.net/ru/npc/'.$id;
$somepage = curl_npc($plink);

	
preg_match_all('#<div class="outer item_info">(.+?)div>#is', $somepage, $arr);

	$table = $arr[1][0];
    //echo $table;
preg_match_all('#ID: (.+?)td>#is', $table, $arr);
$id2 = $arr[1][0];
if($id2 < 1) 
continue;
$n++;	
//echo '<br>ID: '.$id2.'<br><br>';
preg_match_all('#npc">(.+?)span>#is', $table, $name);
//print_r($name);
	$name = $name[1][0];
$name = preg_replace("/[^0-9A-Za-z() \.А-яЁё-]+/u", '', $name);
	$name = preg_replace("/39/u", '', $name);
	//echo 'Имя: '.$name.'<br>';
preg_match_all('#Уровень: (.+?)<br>#is', $table, $lvl);
$lvl = $lvl[1][0];
//echo 'Уровень: '.$lvl.'<br>';
if(preg_match('/Время перерождения/',$table))
	$reg = '#Уровень: '.$lvl.'(.+?)Время перерождения#is';
	else $reg = '#Уровень: '.$lvl.'(.+?)td>#is';
	$arr = array();
	preg_match_all($reg, $table, $arr);
	$types = $arr[1][0];
	//print_r($arr);
	//echo 'Тип:'.$types.' конец типа<br>';
	$types = explode('/', $types);
	//echo $types[0];
	$t1 = $types[0];
	$t2 = $types[1];
	$t1 = preg_replace("/<br>/", '', $t1);
	$t2 = preg_replace("/<br>/", '', $t2);
	$t1 = trim(preg_replace("/</", '', $t1));
	$t2 = trim(preg_replace("/</", '', $t2));
	//echo 'Тип 1:'.$t1.'.<br>Тип 2:'.$t2.'.<br>';
	$npc = 0;
if(preg_match('/npc_icon.png/',$table))
	{$npc = 1;
	//echo '<br>Это NPC<br>';
	}
if(preg_match('/mob_icon.png/',$table))
	{$npc = 2;
	//echo '<br>Это моб<br>';
	}
if($t2 == 'Питомец')
	{$npc = 3;
	//echo '<br>Это питомец<br>';
	}
//qwe("REPLACE INTO `npc` (`npc_id`, `npc_name`, `rank`, `race`, `type`)
//VALUES ('$id', '$name', '$t1', '$t2', '$npc')");

	///Выясняем про дроп	
if(!preg_match('/Добыча с этого NPC/',$somepage)) continue;
if(!preg_match('/map_container/',$somepage))
qwe("UPDATE `npc` set `instance` = 1 WHERE `npc_id` = '$id' AND `type` = 2");		
$drgr++;	
$plink = 'http://archeagedatabase.net/query.php?a=npc_drop&id='.$id.'&l=ru';
$somepage = curl_npc($plink);
$arr = array();
preg_match_all('#item--(.+?)"#is', $somepage, $arr);
//print_r($arr);
foreach($arr[0] as $v)
	{
		$str[] = preg_replace("/[^0-9]/", '', $v);
		//echo $str.'<br>';
	}
$lut = array_unique($str);
foreach($lut as $l)
{
	if($l > 0)
 qwe("REPLACE INTO `dropolist` (`npc_id`, `lut_id`) VALUES ('$id', '$l')");
}	
//print_r($str);
//echo implode('<br>',$lut);
	

//echo '<hr>';
usleep(10000);
}
echo 'Обход NPC Завершен.<br>Найдено '.$n.' объектов.<br>
Из них '.$drgr.' содержат лут.';
	?>
