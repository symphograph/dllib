<?php 
require_once 'includs/usercheck.php';
setcookie('path', 'user_prices');
//$user_id = $muser;
//$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);
/*
$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
		die('error_sess');
*/
//if(!$email) exit;

$puser_id = $_GET['puser_id'] ?? $user_id;
$puser_id = intval($puser_id);


?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Цены пользователя</title>
<link href="css/default.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/user_prices.css?ver=<?php echo $ver?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php include_once 'includs/header.php';
$puser_nick = AnyById($puser_id,'mailusers','user_nick')[$puser_id];
	  
?>
<main>
<div id="rent">

	<div class="navcustoms">
	<h2>Цены пользователя <?php echo $puser_nick?></h2><br>
	<div class="prmenu">
	<?php ServerSelect();

if($puser_id != $user_id)
{
	$valutignor = 'AND `prices`.`item_id` NOT in ('.implode(',',IntimItems()).')';
	$chks = ['','checked'];
	$chk = intval(IsFolow($user_id,$puser_id));
	?>
	<label for="folw" data-tooltip="Если цена этого пользователя новее Вашей, она будет использована в расчетах.">
	Доверять этим ценам
		<input type="checkbox" <?php echo $chks[$chk]?> name="sfolow" id="folw" value="<?php echo $puser_id?>">
	</label>
	
	<?php
	if($ismobiledevice)
	{
		echo '<p>Если цена этого пользователя новее Вашей, она будет использована в расчетах.</p>';
	}
	?>
	<br>
	<a href="user_prices.php"><button class="def_button">Мои цены</button></a>
	<?php
}else
{
	$valutignor = '';
}
?>
	
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
`prices`.`item_id`, 
`prices`.`auc_price`, 
`prices`.`time`,
`items`.`item_name`,
`items`.`icon`,
`items`.`basic_grade`,
`items`.`item_id` IN (".$based_prices.") as `isbased`,
user_crafts.isbest,
user_crafts.isbest = 3 as ismybuy,
items.craftable
FROM `prices`
INNER JOIN `items` ON `items`.`item_id` = `prices`.`item_id`
AND `prices`.`user_id` = '$puser_id'
AND `prices`.`server_group` = '$server_group'
".$valutignor."
LEFT JOIN user_crafts ON user_crafts.user_id = '$user_id' AND user_crafts.item_id = `prices`.`item_id`
AND user_crafts.isbest > 0
ORDER BY `isbased` DESC, ismybuy DESC, `prices`.`time` DESC
");

//$checks = ['','checked'];

UserPriceList($qwe);

function UserPriceList($qwe)
{
	global $puser_id, $user_id;
	
	foreach($qwe as $q)
	{
		extract($q);
		?><div><?php

		$chk = $isby = '';

		if($craftable)
			$isby = intval($isbest)+1;

		if($puser_id == $user_id)		
		PriceCell($item_id,$auc_price,$item_name,$icon,$basic_grade,$time,$isby);
		else
		PriceCell2($item_id,$auc_price,$item_name,$icon,$basic_grade,$time);
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
$('#rent').on('change','#folw',function(){
	 
	var condition = Number($(this).prop("checked"))+1;

	//console.log(condition);
	
	$.ajax
	({
		url: "hendlers/setfolow.php", // путь к ajax файлу
		type: "POST",      // тип запроса
		dataType: "html",
		cache: false,
		data: {
			sfolow_id: <?php echo $puser_id?>,
			condition: condition
		},
		// Данные пришли
		success: function(data) 
		{
			/*
			$(okid).html(data);
			$(okid).show();
			setTimeout(function() {$(okid).hide('slow');}, 0);
			*/
		}
	});
});	
	
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
	
$('.prices').on('change', 'input[type=checkbox]', function(){
	var item_id = $(this).attr('id').slice(5);
	var okid = $('#responses');
    var dataObj = {
        item_id: item_id,
        value: $(this).is(':checked')
    }
    $.ajax({
        data: dataObj,
        url: 'hendlers/isbuysets.php',
		type: "POST",
        success: function(data){
            console.log('Сервер вернул:' + data);
			$(okid).html(data);
			$(okid).show(); 
			setTimeout(function() {$(okid).hide('slow');}, 2000);
        }
    });
});
</script>
</html>