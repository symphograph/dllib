<?php 

include 'includs/ip.php';
include_once 'includs/config.php';
include_once 'includs/user.php';
if(empty($_POST) or $user_id < 1) exit();
include_once 'functions/functs.php';

if(!empty($_POST['send']) and $user_id > 0)
{
	$uri_from = $_SERVER['HTTP_REFERER'];
	$item_id = intval($_POST['item_id']);
	$report_type = intval($_POST['report_type']);
	$text = Comment($_POST['text']);
	$text = mysqli_real_escape_string($dbLink,$text);
	qwe("INSERT INTO `reports` 
	(`user_id`, `item_id`, `maess`, `report_type`, `time`, `dtime`)
	VALUES 
	('$user_id', '$item_id', '$text', '$report_type', now(), now())
	");
}

$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);
//$ver = 'hgfd56765';
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name=“robots” content=“noindex, nofollow”>
<title>Комментировать</title>
<link href="css/style2.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/Search4.css?ver=<?php echo $ver?>" rel="stylesheet">
<link href="css/customs.css?ver=<?php echo $ver?>" rel="stylesheet">
</head>

<body>

<?php include_once 'pageb/header.html';?>

<div class="top"></div>
<div id="rent">
	<div class="rent_in">
	
	
<div class="ava"><div class="avar"><?php echo $fname.$avatar; ?></div></div>
<div class="clear"></div>
<div class="clear"></div>
      

<div class="all_info">
<p>Оставьте комментарий к предмету</p>
<br>
<div class="clear"></div>
<hr>
<div class="report">
<form method="post" action="">
<?php 
/*	
$query = qwe("SELECT * FROM `report_list`");
foreach($query as $v)
{
		$rep_id = $v['id'];
		$rep_var = $v['report_name'];
	if(in_array($rep_id,[4]) and $_POST['craftable'] == 1) continue;
	if(in_array($rep_id,[5]) and $_POST['craftable'] != 1) continue;
	?>
	<p><input type="radio" name="report_type" id="<?php echo $rep_id;?>" value="<?php echo $rep_id;?>"><label for="<?php echo $rep_id;?>"> <?php echo $rep_var;?></label></p>
	<input type="hidden" name="item_id" value="<?php echo $_POST['item_id'];?>"/>
	<?php
}
*/	
?>
<!--<p><label for="report_type2"> Подробно:</p>-->
<p><textarea style="width: 250px; min-height: 100px; padding: 5px; border-radius: 3px;" name="text" placeholder="Ваш комментарий"></textarea></p>
<input type="submit" class="crft_button" name="send" value="Отправить">
</form>
<br>
<span style="font-size: 14px">
<p>К сожалению, я не смогу помочь в отношении харктеристик экипировки.</P>
<p>В этом смысле вам помогут <a href="http://archeagedatabase.net/">тут</a> или <a href="http://archeagecodex.com">тут</a></P>
<p>По ГС (рейтингу экипировки) вам помогут <a href="https://aacalc.ru">здесь</a>.</P>
</span>
</div>
</div></div></div>
<?php 
include_once 'pageb/footer.php'; ?>

</body>
</html>