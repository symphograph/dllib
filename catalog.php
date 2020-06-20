<?php
include_once 'includs/usercheck.php';
setcookie('path', 'catalog');
//include_once 'includs/user.php';
$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);

$item_sgroup = $_GET['item_sgroup'] ?? 1;
$item_sgroup = intval($item_sgroup);
$item_sgroup = $item_sgroup ?? 1;

$item_id = $_GET['item_id'] ?? $_GET['query_id'] ?? 0;
$item_id = intval($item_id);
if($item_id  and !$BotName)
{
	$cooktime = time()+60*60*24*360;
	setcookie("item_id",$item_id,$cooktime);
	header("Location: catalog.php"); exit;
}

if(!empty($_GET['query']) and !$BotName)
	{header("Location: catalog.php"); exit;}

if(!empty($_COOKIE['item_id']))
{
	$item_id = intval($_COOKIE['item_id']);
}

//$sgroupWays = ['basedprices','custprofs','userprofile'];
//$sgroupWay = $sgroupWays[$item_sgroup];
//jjjj
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, крафкулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Предметы</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/items.css?ver=<?php echo md5_file('css/items.css')?>" rel="stylesheet">
<link href="css/right_nav.css?ver=<?php echo md5_file('css/right_nav.css')?>" rel="stylesheet">
<link href="css/catalog_area.css?ver=<?php echo md5_file('css/catalog_area.css')?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="js/search.js?ver=<?php echo md5_file('js/search.js')?>"></script>

<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php include_once 'includs/header.php';?>

<?php /*?><div class="top"></div><?php */?>
<main>
<div id="rent">
	
	<div class="navcustoms">

<?php
//dbCleaner();
$qwe = qwe("
SELECT * FROM `item_subgroups`
WHERE `visible_ui` > 0
");	
foreach($qwe as $q)
{
	extract($q);
?>
	<div onClick="ContentLoad('<?php echo $sgr_id?>')">
	<div class="navicon" data-tooltip="<?php echo htmlspecialchars($description)?>" style="background-image: url(../img/icons/50/<?php echo $icon?>);"></div>
	<div class="navname"><?php echo $sgr_name?></div>
	</div>
<?php
}
		$agent = get_browser(null, true);
		$ismobiledevice = $agent['ismobiledevice'];
		//var_dump($ismobiledevice);
?>
		</div>
	<div class="searcharea">
		
		<div class="search">
			<div id="snav"><div id="searchbtn"></div></div>
			<div><input type="text" id="search_box" name="squery" value="" autocomplete="off"/>
		<div id="search_advice_wrapper"></div>
		</div></div>
	</div>
	<div id="tiptop"></div>
	

	<div id="rent_in" class="rent_in">

	
	
	
	<div class="all_info_area" id="all_info_area">
	<div class="all_info" id="all_info">
	<input type="hidden" id="current" name="current" value="<?php echo $item_id?>">
	<input type="checkbox" id="nav-toggle" hidden="" checked>
	<div class="nav categories" id="categories">

	</div>
	<div id="right">
		<div class="items_head" id="items_head">
			<h3 id="categ_name"></h3>
			<div class="little_buttons">
				<input type="radio" id="bar" name="view" value="0" <?php if(!$view) echo 'checked'?>>
				<label class="bar" for="bar"></label>
				<input type="radio" id="list" name="view" value="1" <?php if($view) echo 'checked'?>>
				<label class="list" for="list"></label>
			</div>
		</div>
		<div class="clear"></div>
		<div id="items"></div>
		<div class="clear"></div>
	</div>
	</div>
	
	</div>
<!--<div class="clear"></div>-->

</div>
<!--<div class="clear"></div>-->
</div>
</main>
<?php include_once 'pageb/footer.php';?>
</body>
<script type="text/javascript" src="js/Catalog.js?ver=<?php echo md5_file('js/Catalog.js')?>"></script>
<script type='text/javascript'>
	//window.onresize = function(event) {
   	//document.write('Разрешение экрана: <b>'+window.screen.availWidth+'×'+window.screen.availHeight+'px.</b>'); 
//}
	
window.onload = function() {
	<?php 
	if($item_id)
	{
		echo 'LoadItem('.$item_id.');';
	}else
		echo 'ContentLoad(1);';
	?>
  
	
};

$('#all_info').on('change','input[name="cat_id"], input[name="view"]',function()
{
	$('#search_box').val('');
	QueryItems();
	<?php
	if($ismobiledevice)
	{
	?>
		$('#nav-toggle').prop('checked',false);
	<?php
	}
	?>
	
});


	
	

	
$('#snav').on('click','#backbtn',function(){
	var sgr_id = $('#sgr_id').val();
	var cat_id = $('#categ_id').val();
	//console.log(sgr_id+'_'+cat_id);
	//ContentLoad(sgr_id,cat_id);
	<?php
	if(!$ismobiledevice)
	{
	?>
		$('#nav-toggle').prop('checked',true);
	<?php
	}
	?>
	
	$('#snav').html('<div id="searchbtn"></div>');
	$('#items_head').removeClass('hidden');
	QueryItems(cat_id);
	
});	

	
<?php
if($myip)
{
	?>
	function UpdateItem(item_id)
	{
		$.ajax
		({
			url: "pars_items.php", // путь к ajax файлу
			type: "POST",      // тип запроса

			data: {
				start_id: item_id,
				go: "go"
			},

			// Данные пришли
			success: function(data) 
			{
				//LoadItem(item_id);
				console.log('ok');
			}
		});
	}
	<?php
}
?>
    $('#all_info').on('click','#mitemname, .item_name',function(){
        var txt = $(this).text();
        selectText(this.id);
        document.execCommand("copy");
        $(this).html(txt +=' ');
        $(this).html(txt);

    });
    function selectText(elementId) {
        var doc = document,
            text = doc.getElementById(elementId),
            range,
            selection;

        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }
/*
 $('body').on( {
   'mouseenter':function() { console.log("enter"); },
   'mouseleave':function() { console.log("leave"); }
});*/

</script>

</html>