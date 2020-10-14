<?php
setcookie('path', 'packtable');
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';

$userinfo_arr = UserInfo();
if (!$userinfo_arr) {
    header("Refresh: 0");
    die();
}

$ver = random_str(8);
$aa_ver = '6.5.3';
?>


<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="yandex-verification" content="<?php echo $yandex_key?>" />
<meta name = "description" content = "Таблица цен на паки в Archeage <?php echo $aa_ver?>"/>
  <meta name = "keywords" content = "товары фактории, паки <?php echo $aa_ver?>, archeage, архейдж, аркейдж, региональные товары, таблица паков, сколько стоят паки, цена паков" />
<title>Таблица цен на паки <?php echo $aa_ver?></title>

<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/packtable.css?ver=<?php echo md5_file('css/packtable.css')?>" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';
/*
$per = 130;
if(isset($_POST['perc']) and ctype_digit($_POST['perc']))
	$per = $_POST['perc'];
*/
?>
<main>
<div class="input1" id="rent">

	
<div class="menu_area">	
<form method="post" action="packtable.php" name="packsettings" id="packsettings">


<div class="navcustoms top">
<h2 class="p_title"><span>Паки <?php echo $aa_ver?> при</span><?php perselect(130);?><span>%</span></h2>
	<div class="siol">
		<div class="nicon_out ">
			<a href="/user_customs.php">
			<label class="navicon" for="usercustoms" style="background-image: url(../img/icons/50/icon_item_0060.png);"></label>
			</a>
			<div class="navname">Настройки</div>
		</div>
	</div>

	<div class="siol">
		<div class="nicon_out ">
			<a href="/packres.php">
			<label class="navicon" style="background-image: url(../img/icons/50/icon_item_1314.png);"></label>
			</a>
			<div class="navname">Ресурсы для паков</div>
		</div>
	</div>
	<div class="siol">
		<div class="nicon_out ">
			<input type="checkbox" id="siol" name="siol" value="5">
			<label class="navicon" for="siol" style="background-image: url(../img/icons/50/icon_item_3368.png);"></label>
			<div class="navname">Сиоль</div>
		</div>
	</div>
<?php
	

function perselect($per)
{

	?><select name="perc" class="perc"><?php

	$f = 135; $sel_per = '';
	for ($i = 1; $i <= 17; $i++)
	{
		$f = $f-5;
		if($f==$per) $sel_per = 'selected';
		echo '<option value="'.$f.'" '.$sel_per.'>'.$f.'</option>';
		$sel_per = '';
	};

	?></select><?php
}
	$ssiol = 5; $x_siol = '<b>X</b>'; $siol_on_off = 0; $siol_title = 'Включить Сиоль';

		
	
	if($siol == 5)
	{
		$ssiol = 0;
		$x_siol = ' ';
		$siol_on_off = 1;
		$siol_title = 'Отключить Сиоль';
	}	
?>
</div>

<div class="select_area">
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/../functions/packs_menu_t1.php';?>
</div>
<div class="clear"></div>
<hr>

</form>
<div id="tiptop"></div>
</div>
	
	<div class="jdunarea">
		
		<div id="jdun" class="jdun">
			<h2>Считаю...</h2>
			<div class="rot" style="background-image: url(../img/perdaru.png)"></div>
		</div>
	</div>
<div class="rent_in"><div class="all_info_area">
<div class="all_info" id="all_info">
	
	<!--<div class="clear"></div>-->

	<div id="input_data"></div>
</div>	
		
	
</div>
</div>
</div>

</div>
<div class="clear"></div>
</main>
</div>
<?php
	include_once 'pageb/footer.php';
	
	?>
</body>
<script type='text/javascript'>
window.onload = function() {
	//setTimeout(function() {$("#xlgames").hide('slow');}, 3000);	
	TipTop();
};
$('#packsettings').on('change','input, select',function(){QueryPacks()});
$('#input_data').ready(function() 
{
	$('#input_data').on('mouseover','div[class="pack_icon"]',function()
	{
		var item_id = $(this).attr('itid');
		var divid = $(this).attr('id');
		if(divid == 0) return false;
		QueryMats(item_id,divid);
		$("#"+divid).attr('id',0);
	
	});
   
})

function QueryMats(id,divid)
{
	var mats = $("#"+divid).next(".pkmats_area");
		$.ajax
	({
		url: "hendlers/pack_mats.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			item_id: id
		},
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$(mats).html(data );
		}
	});
	
}
	
$('main').on('click','div',function(){

	
	
	//return false;
});
	
function TipTop()
{
	
	$.ajax
	({
		url: "hendlers/tiptop.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			tiptop: 1
		},
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$("#tiptop").html(data );
		}
	});
	
}
function QueryPacks()
{ 
	var form = $("#packsettings");
	
	var side = $('input[name=side]:checked',form).val();
	//$("#input_data").html(jdun);
	$jdun = $("#jdun");
	
	if(!side) return($("#input_data").html('<h2>"Восток" или "Запад"?</h2>'));
	console.log(side);
	$jdun.removeClass("jdun"); $jdun.addClass("loading");
	$.ajax
	({
		
		url: "hendlers/packs_list.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: form.serialize(),
		
		dataType: "html",
		cache: false,
		// Данные пришли
		success: function(data ) 
		{
			$jdun.removeClass("loading"); $jdun.addClass("jdun");
			$("#input_data").html(data );
			TipTop();
		}
	});
}
	
$('#all_info').on('input','.pr_inputs',function(){
	//Удаляет цену юзера
	var form_id = $(this).get(0).form.id;
	//console.log(form_id);
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