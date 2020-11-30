<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
$timestart = $_SERVER["REQUEST_TIME_FLOAT"];
if(!$myip) exit;
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';

if(isset($_GET['exit']))
	{
		setcookie('fname', '');
		setcookie('mailid', '');
		setcookie('avatar', '');
		echo '<meta http-equiv="refresh" content="0; url=bestcrafts.php">';
	}


require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';

include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/user.php';


//$server_group = 1; $server = 0;
	
	//$server_group = ServerInfo($user_id);
	//$server = ServerInfo($user_id, 'server');
	
$hrefself = $_SERVER['PHP_SELF'];
$ver = random_str(8);
//$ver = '8765678kfk';

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Крафтолятор рецептов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Крафтолятор archeage</title>
 <link href="css/style.css?ver=<?php echo $ver?>" rel="stylesheet">
 <link href="css/Search4.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/bestlist.css?ver=<?php echo $ver?>" rel="stylesheet">
 <link href="css/comments.css?ver=<?php echo $ver?>" rel="stylesheet">
 <?php
///Операции с юзерскими настройками
include $_SERVER['DOCUMENT_ROOT'].'/../includs/user_configs.php';

?>
<!--<script src="https://yandex.st/jquery/1.7.2/jquery.min.js"></script>-->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php

include_once 'pageb/header.html';
include_once 'cat-funcs.php';
include_once 'edit/funct-obhod2.php';
$prof_q = qwe("SELECT * FROM `user_profs` where `user_id` ='$user_id'");
$query = qwe("
SELECT 
`crafts`.`craft_id`,
items.item_id,
items.item_name,
crafts.rec_name
FROM `crafts`
INNER JOIN `items`
ON `items`.`item_id` = `crafts`.`result_item_id`
AND crafts.on_off = 1 AND crafts.deep = 0
AND items.personal != 1
INNER JOIN prices WHERE prices.item_id = items.item_id
GROUP BY item_id");
foreach($query as $q)
	{
		extract($q);
		$itemq = $item_id;
		CraftsObhod($item_id,$dbLink,$user_id,$server_group,$server,$prof_q);
		echo $item_id;
		unset($total, $itog, $craft_id, $rec_name, $item_id, $lost, $forlostnames, $orcost, $repprice, $honorprice, $dzprice, $soverprice, $mat_deep, 
			$crafts, $deeptmp, $craftsq, $icrft);
	}

?>

<div class="top"></div>
	<div id="rent"><div class="rent_in">
		

		<div class="top"></div>

		<div class="search_area">
		
		
	</div>
		
   <div class="ava"><div class="avar"><?php echo $fname.$avatar; ?></div></div>
   <br>	
      <div class="line"><br><hr></div>
      <div class="money_area"></div>

<div class="all_info">
<?php

$query = qwe("
SELECT
	crafts.craft_id,
	crafts.rec_name,
	IF(crafts.rec_name = items.item_name,if(crafts.dood_name !='',crafts.dood_name,crafts.rec_name),if(crafts.rec_name != '',crafts.rec_name,items.item_name)) as rec_info,
	crafts.labor_need,
	crafts.dood_name,
	user_crafts.labor_total,
	items.item_name,
	items.item_id,
	items.category,
	items.icon,
	user_crafts.craft_price,
	crafts.result_amount,
	prices1.price,
	prices1.myprice,
	prices1.time,
	round(prices1.price * 0.9) - user_crafts.craft_price AS profit,
	round((prices1.price * 0.9 - user_crafts.craft_price ) / user_crafts.labor_total ) AS pr_or,
	crafts.deep
FROM
	crafts
	INNER JOIN items ON crafts.result_item_id = items.item_id
	INNER JOIN user_crafts ON crafts.craft_id = user_crafts.craft_id 
	AND user_crafts.user_id = '$user_id' 
	AND user_crafts.isbest in (1,2)
	AND user_crafts.craft_id NOT in (SELECT craft_id FROM hide_cl WHERE user_id = '$user_id')
	INNER JOIN (SELECT
	prices.item_id,
	prices.time,
	myprices.auc_price as myprice,
	t1.user_id = '$user_id' as ismy,
	if(myprices.auc_price>0,myprices.auc_price,prices.auc_price) as price
FROM
	(
SELECT
	user_id,
	auc_price,
	item_id,
	max(`time` ) AS max_pr_t,
	`time` 
FROM
	prices 
WHERE
	server_group = '$server_group' 
GROUP BY
	item_id 
ORDER BY
	item_id 
	) AS t1
	INNER JOIN prices ON t1.item_id = prices.item_id 
	AND t1.max_pr_t = prices.time 
	LEFT JOIN (SELECT user_id, item_id, auc_price FROM prices WHERE user_id = '$user_id' AND server_group = 2) as myprices ON t1.item_id = myprices.item_id
ORDER BY
	prices.item_id) 
	AS prices1 ON crafts.result_item_id = prices1.item_id 
GROUP BY craft_id
ORDER BY
	pr_or DESC");
	?>
	<div class="toprow">
		<div class="col1"></div><div class="stolb"></div>
		<div class="col2">Себестоимость<br>Прибыль</div><div class="stolb"></div>
		<div class="col2">Монетизация ОР<br>(со всей цепочки)</div><div class="stolb"></div>
		<div class="col3"></div>
	</div><hr>
	<?php
	$i = 0;
	foreach($query as $q)
	{$i++;
	 	$row = intval($i % 2 === 0);
		extract($q);
	// var_dump($craft_id);
	 	$craft_price = esyprice($craft_price);
	 	$craft_price = str_replace('width="15" height="15"','width="10" height="10"',$craft_price);
	 	$profit = esyprice($profit);
	 	$profit = str_replace('width="15" height="15"','width="10" height="10"',$profit);
	 	$pr_or = esyprice($pr_or);
	 	$pr_or = str_replace('width="15" height="15"','width="10" height="10"',$pr_or);
	 	$pricestr = esyprice($price);
	 	$pricestr = str_replace('width="15" height="15"','width="10" height="10"',$pricestr);
	 	if($myprice > 0)
		{
			$you = ' вы указали';
			$color = 'style="background-color: lightgreen"';
		}else 	
	 	{
			$you = ' кто-то указал';
		 	$color = '';	
		}
	?>
	
	<div class="row<?php echo $row;?>" id=<?php echo $craft_id;?>>
		<div class="col1">
			<div class="img_name">
			<div>
			<!--<div>
			<div class="itemprompt" data-title="Смотреть рецепт">-->
				<a href="../catalog.php?query_id=<?php echo $item_id;?>" target="_blank">
				<div class="pack_icon" style="background-image: url(img/icons/50/<?php echo $icon;?>.png)">
				<!--<div class="itdigit">15%</div>-->
				</div></a>
			</div>
			<div class="rec_item_names">
			<div class="critem_name"><?php echo $item_name;?></div>
			<div class="rec_name"><?php echo $rec_info;?></div>
			</div>
			</div>
		</div><div class="stolb"></div>
		<div class="col2"><?php echo $craft_price;?><br><?php echo $profit;?></div><div class="stolb"></div>
		<div class="col2"><?php echo $pr_or;?></div><div class="stolb"></div>
		
		<div class="col3">
			<?php
	 		echo date('d.m.Y',strtotime($time)),$you;?><br>
			<div class="priceform">
			
			<form action="" method="post" id="pr_<?php echo $item_id;?>">
			<?php MoneyLineBL($price,$item_id,$color)?>
			<input type="hidden" name="item_id" value="<?php echo $item_id;?>"/>
			<button type="button" onClick="HideCraft(<?php echo $craft_id;?>); return false;">Скрывать</button>
			
			</form>
			<div class="ok" id="ok<?php echo $item_id;?>" style="display: none"></div>
			</div>
			
			</div>
		<div class="stolb"></div>
		<div class="ok" id="ok<?php echo $item_id;?>" style="display: none"></div>
	</div>
	<?php
		
	}
?>
<hr>
<?php
$query = qwe("
SELECT
crafts.craft_id,
crafts.rec_name,
items.item_name,
items.item_id,
items.icon,
crafts.dood_name,
IF(crafts.rec_name = items.item_name,if(crafts.dood_name !='',crafts.dood_name,crafts.rec_name),if(crafts.rec_name != '',crafts.rec_name,items.item_name)) as rec_info

FROM
	hide_cl
	INNER JOIN crafts ON hide_cl.craft_id = crafts.craft_id 
	AND hide_cl.user_id = 893
	INNER JOIN items ON crafts.result_item_id = items.item_id");
if(mysqli_num_rows($query)>0)
{
	?>
	<details><summary>Скрытые рецепты</summary>
	<?php
	$i=0;
	foreach($query as $q)
	{$i++;
	 	$row = intval($i % 2 === 0);
		extract($q);
		?>
		<div class="row<?php echo $row;?>" id=<?php echo $craft_id;?>>
		<div class="col1">
			<div class="img_name">
			<div>
			<!--<div>
			<div class="itemprompt" data-title="Смотреть рецепт">-->
				<a href="../catalog.php?query_id=<?php echo $item_id;?>" target="_blank">
				<div class="pack_icon" style="background-image: url(img/icons/50/<?php echo $icon;?>.png)">
				<!--<div class="itdigit">15%</div>-->
				</div></a>
			</div>
			<div class="rec_item_names">
			<div class="critem_name"><?php echo $item_name;?></div>
			<div class="rec_name"><?php echo $rec_info;?></div>
			</div>
			</div>
		</div><div class="stolb"></div>
		<button type="button" onClick="ReturnCraft(<?php echo $craft_id;?>); return false;">Не скрывать</button>
		</div>
		<?php
	}
	
	?>	
	</details>
	<?php
}
?>

</div><div style="clear:both;"><br><br><br><br></div>


<!--Закрываем info--></div>
<!--Закрываем rent и rent_in--></div><div style="clear:both;"><br></div></div>
<?php 
	
	include_once 'pageb/footer.php'; 

	?>
</body>
<script type="text/javascript">
function SetPrice(item_id)
{ 
	var form = $("#pr_"+ item_id);
	var okid = "#ok"+ item_id;
	
	$.ajax({
		url: "hendlers/setprcl.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: form.serialize(),
		dataType: "html",
		cache: false,
	 // Данные пришли
	 success: function(data ) {
	   $(okid).html(data );
	$(okid).show(); 
	setTimeout(function() { $(okid).hide('slow'); }, 0);
	  }
	});
}

function HideCraft(craft)
{ 	
	$.ajax({
		url: "hendlers/hidecrft.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			craft_id: craft
		},
	
	 // Данные пришли
	 success: function(data ) {
	   //$(okid).html(data );
		 //console.log(data );
	$("#"+craft).hide();
	  }
	});
}

function ReturnCraft(craft)
{ 	
	$.ajax({
		url: "hendlers/returncrft.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			craft_id: craft
		},
	
	 // Данные пришли
	 success: function(data ) {
	   //$(okid).html(data );
		 //console.log(data );
	$("#"+craft).hide();
	//setTimeout(function() { $(okid).hide('slow'); }, 0);
	  }
	});
}
</script>
</html>