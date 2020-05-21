<?php
include_once 'includs/usercheck.php';
setcookie('path', 'user_customs');
//include_once 'includs/user.php';
$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);

$customType = $_GET['type'] ?? 0;
$customType = intval($customType);

$custWays = ['basedprices','custprofs','userprofile'];
$custWay = $custWays[$customType];
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Настройки</title>
<link href="css/default.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/customs.css?ver=<?php echo $ver?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="js/tooltips.js?ver=<?php echo $ver?>"></script>
</head>

<body>

<?php include_once 'includs/header.php';?>

<?php /*?><div class="top"></div><?php */?>
<main>
<div id="rent">
<div class="menu_area">	
	<div class="navcustoms">
		<div onClick="ContentLoad('<?php echo $custWays[0]?>')">
			<div class="navicon" style="background-image: url(../img/icons/50/icon_item_1766.png);"></div>
			<div class="navname">Базовые цены</div>
		</div>
		<form method="POST" action="serverchange.php" name="server">
			<select name="serv" id="server" class="server" onchange="this.form.submit()">
			<?php
			$query = qwe("SELECT * FROM `servers`");
			SelectOpts($query, 'id', 'server_name', $server, false);	

			?>
			</select>
		</form>
		<div  onClick="ContentLoad('<?php echo $custWays[1]?>')">
			<div class="navicon" style="background-image: url(../img/profs/Обработка_камня.png);"></div>
			<div class="navname">Уровни ремесла</div>
		</div>
	</div>
<div class="modes">Режимы</div>
<?php modes($mode); ?>
</div>
<div id="rent_in" class="rent_in">
	
	


<?php 
function modes($mode)
{
	$chks = ['','checked'];
	$mode_names = ['','Наивность', 'Маргинал', 'Хардкор'];
	$mode_tooltips = 
	[
		'',
		'Режим для новичка.<br>Предпочитает Ваши цены или более новые из доверенных.<br>Если их нет, ищет у других.<br>Спрашивает только, если никто и никогда не указывал цену.<br>',
		'Не видит ничьих цен, кроме Ваших и тех, кому Вы доверяете.<br>Предпочитает более новые.<br>ОР, РР, Честь и прочие субъективные предпочитает Ваши независимо от их новизны.',
		'Видит только Ваши цены.<br>В любой непонятной ситуации будет спрашивать.'
	];
?>
	<form id="fmodes" method="post" action="edit/setmode.php">
		<div class="modes">
			<?php
			foreach($mode_names as $mnk => $mnv)
			{
				if(!$mnv) continue;
				?>
				<label data-tooltip="<?php echo $mode_tooltips[$mnk];?>">
					<div>
						<input type="radio" <?php if($mode == $mnk) echo 'checked'?> name="mode" value="<?php echo $mnk;?>" onchange="this.form.submit()"/>
						<?php echo $mnv;?>
					</div>
				</label>
				<?php
			}
			?>	
		</div>
	</form>
	<hr>
<?php
}
?>
<div class="clear"></div>
<div class="all_info" id="all_info">
<div id="items">
</div>




</div>
</div></div>
</main>
<?php 
include_once 'pageb/footer.php'; ?>
</body>
<script type='text/javascript'>
	//window.onresize = function(event) {
   	//document.write('Разрешение экрана: <b>'+window.screen.availWidth+'×'+window.screen.availHeight+'px.</b>'); 
//}
	
window.onload = function() {
  ContentLoad("<?php echo $custWay?>");
};

function ContentLoad(custway)
{
	var needelement = "#items";
	var url = "hendlers/" + custway + ".php";
	//console.log(url);
	$.ajax
	({
			url: url,
			type: "POST",
			datatype: "html",
			cache: false,
			data: 
			{
				usercustoms: "1"
			},
		
			 // Данные пришли
			success: function(data ) 
			{
				$(needelement).html(data );
			}
	})
}
	
function SetProf(prof_id)
{ 
	var lvl = $("#prof_"+ prof_id).val();
	var okid = "#PrOk_"+ prof_id;
	//var okid = #;
	$.ajax({
		url: "hendlers/setprof.php", // путь к ajax файлу
		type: "POST",      // тип запроса
		dataType: "html",
		cache: false,
		data: {
			prof_id: prof_id,
			lvl: lvl
		},
		
	 // Данные пришли
	 success: function(data ) {
		
	   $(okid).html(data );
	$(okid).show(); 
	setTimeout(function() { $(okid).hide('slow'); }, 500);
		 console.log(data);
	  }
	});
}

$('#all_info').on('input','.pr_inputs',function(){
	//Удаляет цену юзера
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

function AucraftDel(item_id)
{ 	
	$.ajax
	({
		url: "hendlers/aucraftdel.php", // путь к ajax файлу
		type: "POST",      // тип запроса

		data: {
			item_id: item_id
		},

		// Данные пришли
		success: function(data) 
		{
			$("#aucraft_"+item_id).hide();
		}
	});
}
	
$('#all_info').on('click','.itim',function(){
	var item_id = $(this).attr('id').slice(5);
	var url = 'catalog.php?item_id='+item_id;
	window.location.href = url;
});
</script>
</html>