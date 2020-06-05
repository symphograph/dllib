<?php 
require_once 'includs/usercheck.php';
setcookie('path', 'packres');

$ver = random_str(8);

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Ресурсы для паков</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/user_prices.css?ver=<?php echo md5_file('css/user_prices.css')?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="js/setbuy.js?ver=<?php echo md5_file('js/setbuy.js')?>"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php include_once 'includs/header.php';
//$puser_nick = AnyById($puser_id,'mailusers','user_nick')[$puser_id];
	  
?>
<main>
<div id="rent">

	<div class="navcustoms">
	<h2>Ресурсы для паков</h2><br>
	<div class="prmenu">
	<?php ServerSelect();?>
	
	<br>
	<a href="user_prices.php"><button class="def_button">Мои цены</button></a>
	

	<a href="user_customs.php"><button class="def_button">Настройки</button></a>
	</div><br><hr><div class = "responses"><div id = "responses"></div></div>
	</div>

<div id="rent_in" class="rent_in">
<div class="clear"></div>

<div class="all_info_area">
<div class="all_info" id="all_info">
<div id="items">
<div class="prices">
<?php
$qwe = qwe("
SELECT 
items.item_id, 
items.item_name, 
items.craftable,  
items.personal,
items.icon,
items.basic_grade,
prices.auc_price,
prices.time,
isbest,
(isbest = 3) as isbuy
FROM
(
	SELECT item_id, result_item_id 
	FROM craft_materials 
	WHERE result_item_id IN 
	(
		SELECT DISTINCT item_id 
		FROM packs WHERE item_id IN 
		(SELECT result_item_id FROM crafts WHERE on_off)
	)
	OR result_item_id in (49003, 49033, 49034)
	OR result_item_id in 
	(
		SELECT item_id 
		FROM craft_materials 
		WHERE result_item_id 
		IN  (49003, 49033, 49034)
	)
	GROUP BY item_id
) as tmp
INNER JOIN items 
	ON items.item_id = tmp.item_id 
	AND items.on_off 
	AND items.item_id != 500
LEFT JOIN prices 
	ON prices.user_id = '$user_id' 
	AND prices.item_id = items.item_id 
	AND prices.server_group = '$server_group'
LEFT JOIN user_crafts 
	ON user_crafts.user_id = '$user_id' 
	AND user_crafts.item_id = items.item_id 
	AND user_crafts.isbest > 0
	ORDER BY isbuy DESC, item_name
");

//$checks = ['','checked'];

UserPriceList($qwe);

function UserPriceList($qwe)
{
	global $user_id;
	
	foreach($qwe as $q)
	{
		extract($q);
		?><div><?php

		$isby = '';

		if($craftable)
			$isby = intval($isbest)+1;
		if(!$time)
			$time = '01-01-0000';
		
		
			$pr_arr = PriceMode($item_id,$user_id) ?? false;
			if($pr_arr)
			{
				$auc_price = $pr_arr['auc_price'];
				$time = $pr_arr['time'];
			}
		
		PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade,$time,$isby);
	
		?>
		</div><?php
	}	
}
?>
</div>



</div>
</div></div>
</div></div>
</main>
<?php 
function MyPrices($user_id)
{
	
}

include_once 'pageb/footer.php'; ?>
</body>
<script type='text/javascript'>
window.onload = function() {

$(".small_del").show();
	
};

	
$('#all_info').on('input','.pr_inputs',function(){
	
	var form_id = $(this).get(0).form.id;
	//var name = $(this).attr("name");
	
	SetPrice(form_id);
	
});
	
function SetPrice(form_id)
{ 
	var form = $("#"+form_id);
	
	var item_id = form_id.slice(3);
	var okid = "#PrOk_"+item_id;

	$.ajax
	({
		url: "hendlers/setprcl.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: form.serialize(),
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$(okid).html(data );
			$(okid).show(); 
			setTimeout(function() {$(okid).hide('slow');}, 0);
			$("#prdel_"+item_id).show();
		}
	});
}

$('#all_info').on('click','.small_del',function(){
	//Удаляет цену юзера
	var form_id = $(this).get(0).form.id;
	var item_id = form_id.slice(3);
	var okid = "#PrOk_"+item_id;

	$.ajax
	({
		url: "hendlers/setprcl.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: 
			{
				del: 'del',
				item_id: item_id
			},
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$(okid).html(data );
			$(okid).show(); 
			setTimeout(function() {$(okid).hide('slow');}, 0);
			
			$("#prdel_"+item_id).hide('slow');
			$("#"+form_id).find("input[type=number]").val("");
		}
	});
	
});
	
$('#all_info').on('click','.itim',function(){
	var item_id = $(this).attr('id').slice(5);
	var url = 'catalog.php?item_id='+item_id;
	window.location.href = url;
});
</script>
</html>