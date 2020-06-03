<?php 
//var_dump($_POST); die;
setcookie('path', 'catalog');
$timestart = $_SERVER["REQUEST_TIME_FLOAT"];

include_once 'includs/usercheck.php';

 $itemq = '';
 $item_id = '';
$u_amount = 0;
if(!empty($_POST['u_amount']))
	$u_amount = intval($_POST['u_amount']);
if($u_amount == 0)
	$u_amount = 1;
 //include_once 'includs/config.php';

if(isset($_GET['query']))
$itemq = mysqli_real_escape_string($dbLink,$_GET['query']);

if(!empty($_GET['query_id']))
{
	$item_id = $_GET['query_id'] ?? 0;
	$item_id = intval($item_id);
}



if($item_id > 0) $itemq = $item_id;
elseif(!empty($_COOKIE['last_item']))
$item_id = intval($_COOKIE['last_item']);
 $pump = false;
//ищем id в базе, если получили только имя
if(!empty($itemq) and $item_id=='')
	 {
		 $id_q = qwe("SELECT `item_id`, `item_name` FROM `items` WHERE `item_name` = '$itemq'
		 ORDER BY `craftable` DESC, `ismat` DESC, `personal` LIMIT 1");
		 foreach($id_q as $q)
		 {
			$item_id =  $q['item_id'];
		 }
	 }
//Ищем имя в базе, если получили только id в имени
if(ctype_digit($itemq) and empty($_GET['query_id']))
	$item_db = qwe("SELECT * FROM `items` where `on_off` = 1 AND `item_id` = '$itemq'");
	else
	{ if($item_id>0)
		$item_db = qwe("SELECT * FROM `items` where `on_off` = 1 AND `item_id` = '$item_id'");
		elseif(empty($_COOKIE['last_item']))
		$item_db = qwe("SELECT * FROM `items` where `on_off` = 1 AND `item_name` = '$itemq'");
	 	
	}

$arritem = mysqli_fetch_assoc($item_db);
$item_id = $arritem['item_id'];
$itemq = $arritem['item_name'];
$by_npc = $arritem['is_trade_npc'];

setcookie('last_item', $item_id);

if(ctype_digit($itemq)) 
	$itemq = $arritem['item_name'];
//$item_link= str_replace(" ","+",$itemq);
include 'includs/user.php';


$server_group = 1; $server = 0;
	
	$server_group = ServerInfo($user_id);
	$server = ServerInfo($user_id, 'server');

$hrefself = $_SERVER['PHP_SELF'];
$ver = random_str(8);
//$ver = '8765678kfk';

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Умный калькулятор archeage</title>
 
 <link href="css/style.css?ver=<?php echo $ver?>" rel="stylesheet">
 <link href="css/Search4.css?ver=<?php echo $ver?>" rel="stylesheet">
 <link href="css/comments.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/default.css?ver=<?php echo $ver?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=0.4">
 <?php
///Операции с юзерскими настройками
include 'includs/user_configs.php';

?>
<script src="https://yandex.st/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
window.onload = function()
{window.scroll(0, 220 );}
</script>

 
 <!--<script src="//yastatic.net/jquery/3.1.1/jquery.min.js"></script>-->
 <script type="text/javascript" src="TextChange3.js"></script>

</head>

<body>

<?php include_once 'includs/header.php';
?>

<main>
	<div id="rent"><div class="rent_in">
		<form action="" id="search_form" method="GET">

		<div class="top"></div>

		<div class="search_area">
		<input type="search" name="query" id="search_box" autofocus onchange="submit" 
		value="<?php echo $itemq;?>" autocomplete="off" placeholder="Начинаем вводить имя предмета">
		<input type="hidden" id="search_box2" name="query_id" value="">
		<div id="search_advice_wrapper"></div>
		</form>
       <?
			
		if(!empty($_GET['query']) and $item_id=='')
		   {echo '<br><br><div>Выбирай из выпадающего списка. Иначе я не понимаю.</div>';
			exit();}
	   ?>
	</div>
		
		<?php 
		if(preg_match('/Груз зрелого сыра/iu', $itemq))
		$itemq = 'Груз зрелого сыра';
		if(preg_match('/Груз компоста/iu', $itemq))
		$itemq = 'Груз компоста';
		if(preg_match('/Груз меда /iu', $itemq))
		$itemq = 'Груз меда';
		if(preg_match('/Груз домашней наливки/iu', $itemq))
		$itemq = 'Груз домашней наливки';
		?>	
   
   <div>

	</div>
	
      <div class="line" id="scroll"><br><hr></div>
      <div class="money_area"></div>

<div class="all_info">
<div class="left">
<?php 
if(empty($_GET['query']) and empty($_GET['query_id']) and empty($_COOKIE['last_item']))
{   
	echo '</div></div></div></div>';
	include_once 'pageb/footer.php';
	exit();
}

	
//$time1 = microtime(true) - $timestart;
$global_item_id = $item_id;
//ВЫЯСНЯЕМ ЮЗЕРСКУЮ ЦЕНУ ПРЕДМЕТА
$auc_price = false ; $foundprice = false; $serv_price = 0; $myprice = false;
$price_q = qwe("
SELECT * FROM `prices` 
WHERE `server_group` = '$server_group'
ORDER BY `time` DESC");
$spec_items = array(2, 3, 4, 5, 23633);
	if($arritem['personal'] < 1 or in_array($item_id, $spec_items))
		$auc_price = PriceMode($item_id,$user_id)['auc_price'] ?? false;
$forchenge = '';
	if((!$auc_price) and (!$by_npc)) 
		$forchenge = ' (Сохраните <b>свою</b> цену)';
	
if($by_npc == 1)
echo 'Это продают NPC';
$descr = $arritem['description'];
//if($arritem['next'] == 1) $descr = 'Это проходной предмет';
$personal = $arritem['personal'];
$craftable = $arritem['craftable'];
$category = $arritem['category'];
$categ_id = $arritem['categ_id'];
$basic_grade = $arritem['basic_grade'] ?? 1;
$t_lvl = $arritem['lvl'];

$query_basic_grade = qwe("SELECT * FROM `grades` WHERE `id` = '$basic_grade'");
foreach($query_basic_grade as $qbg)
{
	$grade_color = $qbg['color'];
	$grade_name = $qbg['gr_name'];
}
?>
<div class="item_name">
<p><b><?php echo $itemq;?></b></p>
<span style="color: '.$grade_color.'"><p><?php echo $grade_name;?></p></span>
<?php
if($myip) echo '<p>Item id: '.$item_id.'</p>';
echo '<p>Категория: '.$category.'</p>';
if($personal == 1)
echo '<span style="color: rgba(21,98,180,1.00)"><p>Персональный предмет</p></span>';
if($t_lvl > 0)
echo 'Этап изготовления:<br><b>T'.$t_lvl.'</b></p>';
$top_icon = mysqli_real_escape_string($dbLink,$arritem['icon']) ;

?>
</div>
 
<div class="item_top">
<div class="itemprompt" data-title="<?php echo $itemq;?>">
	<div class="top_itim" style="background-image: url(img/icons/50/<?php echo  $top_icon?>.png); border-color: <?php echo $grade_color;?>;">
	<div class="itdigit"></div></div></div>
	<?php echo $descr; ?>
	<p><a href="http://archeagecodex.com/ru/item/<?php echo $item_id;?>" title="Поискать на archeagecodex.com" text-decoration: none; style="color: #6C3F00;" target="_blank">Больше информации</a></p>
	
	</div>
	<?php
		
include_once 'cat-funcs.php';
	
orrepcost($user_id, $server_group);
$HardPersonal = [2,3,4,5,6, 23633, 28586,41488];
/*
if($personal != '1')
{
		$spec_price = false;
		auc_price($itemq, $item_id, $auc_price, $spec_price, $myprice, $user_id);
};
*/
$valuta = '';
$spec_price = false;
if($by_npc)
{
	$valuta = $arritem['price_type'];
	
		 if($valuta == '') 
			 $valuta = 'gold';
	
		echo '<hr>Продаётся у NPC или в лавках за:<br>';
		if($valuta == 'gold')
		{
			$npc_price = $arritem['price_buy'];
			echo esyprice($npc_price);
		}
	else 
	{
		$val_q = qwe("SELECT * FROM `items` WHERE `item_name` = '$valuta' LIMIT 1");

		foreach($val_q as $vq)
		{
			$valuta_id = $vq['item_id'];
		}
		$val_link = '<a href="'.$hrefself.'?query_id='.$valuta_id.'">
		<img src="img/icons/'.str_replace(' ','_',$valuta).'.png" width="15" height="15" alt="'.$valuta.'"/></a>';
		$price_alt = $arritem['price_buy'];
		echo $price_alt.' '.$val_link;
		//auc_price($itemq, $item_id, $auc_price, false, $myprice, $user_id);
	};

};
	
if($personal == '1' and $by_npc != '1' and (!$craftable))
{
	$spec_price = true;
	if (!in_array($item_id, $spec_items))
		$auc_price = $arritem['price_sale'];
	if($item_id == 2)
		$auc_price = $orcost;
};

if($by_npc and $valuta == 'gold')
{}
elseif($category != 'Региональный товар')
auc_price($itemq, $item_id, $auc_price, $spec_price, $myprice, $user_id);

if($myip)
{
	echo '<p><a href="edit/recedit.php?addrec='.$item_id.'" target="_blank">Добавить рецепт</a></p>';
	echo '<p><a href="edit/edit_item.php?item_id='.$item_id.'&item_name='.$itemq.'">Править итем</a></p>';
	echo '<p><a href="edit/item_off.php?item_id='.$item_id.'&item_name='.$itemq.'&item_off=1">Отключить итем</a></p>';
};
?>
	<form method="post"><div class="itemprompt" data-title="Параметры для корректного расчета">
<input type="submit" class="crft_button" formaction="user_customs.php" name="customs" value="Настройки"></div>
<br><br>
</form>

<!--Закрываем левый--></div>
<div class="right">
<?php

//echo '<p>Время выполнения: '.$time1.'</p>';
parent_recs_ecco($item_id, $hrefself, $dbLink);
	

if($craftable == 1) 
{
	//Запрашиваем уровни профессий юзера
	$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
	echo '<div style="clear:both;"></div><div>';
	
	//Спрашиваем, посчитан ли итем
	$bestq = qwe("SELECT * FROM `user_crafts` where  `isbest` > 0 and `item_id` = '$item_id' and `user_id` ='$user_id'");
	$cnt = mysqli_num_rows($bestq);
	//echo count($bestq);
	
	//Если не посчитан, идем считать
	//if($cnt < 1 or isset($_POST['chenge_best']) or $new)
	//{
		include 'edit/funct-obhod2.php';
		include 'includs/recurs.php';
		//include 'includs/all_or.php';
		//$item_link= str_replace(" ","+",$itemq);
	//};

	//Посчитали (или выяснили, что посчитан)
	$item_id = $arritem['item_id'];
	$arrbest = mysqli_fetch_assoc($bestq);
	$craft_id = $arrbest['craft_id'];
	//include 'all_res.php';
	//echo '<hr>';

	          
	orrepcost($user_id, $server_group);
	$matrow = ''; $prof = ''; $crft_nalog = '';
	child_recs_ecco($item_id, $hrefself, $dbLink, $orcost, $matrow, $price_q, $user_id, $auc_price, $forchenge, $itemq, $personal, $prof_q, $crft_nalog, $pump, $grade_color, $u_amount);
}

		
if((!$craftable and $by_npc and $valut_id !=500) or  in_array($global_item_id,[3,4,5,6,23633]))
{
	//Касательно Чести, Рем Репутации, итд.
	if(in_array($global_item_id,[2,3,4,5,6,23633]))
	{
		$valuta = $itemq;
		$valut_id = $global_item_id;
		$val_link = '<a href="'.$hrefself.'?query_id='.$valuta_id.'">
		<img src="img/icons/50/'.$valut_id.'.png" width="15" height="15" alt="'.$valuta.'"/></a>';	
	}
	
	ItemsFromAlterValutes($hrefself, $valuta);
	?>
	<br><br>
	<h3><?php echo $valuta;?>: cредние цены.</h3>
	<p>Средняя цена вашей аукционной группы:
	
	<?php
	$serv_median = RepMedian($server_group, $valuta, $user_id);
	if($serv_median)
	{
		echo '<b>'.$val_link.' за '. esyprice(round($serv_median,0)).'</b>';
		?>
		</p><div class="clear"></div><br><hr>
		
		<?php
		$arr_mon = MonetisationList($hrefself, $val_link, $valuta, $serv_median, $user_id);
		//var_dump($arr_mon);
		
	}	
	else
		echo '<p>Нет данных</p>';
	
	
}
?>
</div>
<!--Закрываем правый-->
</div><div style="clear:both;"><br><br><br><br></div>
<?php 
if(in_array($categ_id,[122,133,171]))
	PackPricesEcco($item_id);

function PackPricesEcco($item_id)
{
	echo  '<hr><br><center><b>Цены при 130%</b></center><br>';
	$query = qwe("
	SELECT
	pack_prices.zone_to,
	`pack_prices`.`pack_price`/`pack_prices`.`mul` AS price,
	pack_prices.valuta_id,
	pack_prices.zone_id,
	zones.zone_name as zto,
	zz.zone_name as zfrom
	FROM
	pack_prices
	INNER JOIN zones ON pack_prices.zone_to = zones.zone_id
	INNER JOIN zones as zz ON pack_prices.zone_id = zz.zone_id
	WHERE
	pack_prices.item_id = '$item_id'
	ORDER BY price DESC
	");
	$i=0;
	
	foreach($query as $q)
	{$i++;
	 	extract($q);
	 /*
	 	if($i == 1)
		{
			?><b>Откуда:</b><?php
		}
	*/	
		
		$price = round($price);
		
		if($valuta_id == 500)
			$price = esyprice($price);
		else
			$price = $price.'<img src="img/'.$valuta_id.'.png?ver=1" width="20"/>';	
		
		?><div style="display: flex; justify-content: space-between; border-bottom: 1px dotted; padding: 2px;">
		<div style="width:240px"><?php echo $zfrom?></div><div>==> </div>
		<div style="width:240px"><?php echo $zto?></div>
		<div><?php echo $price?></div></div><?php

	}
}

if($valuta == 'Кристалл')
{
?>
<p>*Информация о предметах и их стоимости может со временем терять актуальность. Уточняйте её непосредственно в игре.</p>
<?php
}

/*
//Пишем посещение в лог
	qwe("
	INSERT INTO `paje_log` 
	(`user_id`, `item_id`, `ip`, `time`) 
	VALUES 
	('$user_id', '$item_id', '$ip', now())");
*/
?>
<br><br><br>
<div class="clear"></div>
	<div class="comments">
		<h4>Комментарии</h4><hr>
<?php

$query = qwe("SELECT `user_id`, `mailnick`, `avatar`, `mess`, `reports`.`time` 
FROM `reports`
INNER JOIN `mailusers` ON `user_id` = `mail_id`
WHERE `item_id` = '$global_item_id'");
if($query and mysqli_num_rows($query) > 0)

{
	foreach($query as $com)
	{
		$user_ava = $com['avatar'];
		$mess = $com['mess'];
		$mailnick = $com['mailnick'];
		$date = date('d.m.Y', strtotime($com['time']));
		$time = date('H:i', strtotime($com['time']));
	?>
	<div class="comments_row">	
		<div class="user_info">
			<img src="<?php echo $user_ava;?>" width="50" height="50" alt="avatar"/>
			<h5><?php echo $mailnick;?></h5>
			<?php echo $date.'<br>'.$time;?>
		</div>
		<div class="comment">
		<?php echo $mess;?>
		</div>
	</div>
	<div class="clear"></div>
	<br><hr>
	<?php
	}
}
else
echo 'Нет комментариев';
?>
	</div>
	<div class="comments_row">
	<div class="user_info"><?php echo $avatar;?></div>
	<div class="comment">
<form method="post" name="comment" action="comment.php">
<!--<p><label for="report_type2"> Подробно:</p>-->
<input type="hidden" name="item_id" value="<?php echo $global_item_id?>" autocomplete="off" display="none">
<p><textarea class="textarea" name="text" placeholder="Ваш комментарий"></textarea></p>
<?php

if($email)
{
?>
<input type="submit" class="crft_button" name="send" value="Отправить">
<?php
}
else echo '<a href="oauth/mailru.php?path=catalog&query_id='.$global_item_id.'" style="color: #6C3F00; text-decoration: none;"><b>Войдите</b></a>, чтобы оставлять комментарии';
?>
</form>

</div></div>
	<div style="clear:both;"></div>
<br><br>
	<br>

<!--Закрываем info--></div>
<!--Закрываем rent и rent_in--></div><div style="clear:both;"><br></div></div>
</main>
<?php 
	
	include_once 'pageb/footer.php'; 

	?>

</body>
</html>