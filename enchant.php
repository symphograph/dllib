<?php
exit(header("Location: catalog.php", TRUE, 302));
include 'includs/ip.php';
include 'includs/config.php';
include 'functions/enchant_values.php';
?>


<!doctype html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=0.7">
<meta name = "description" content = "Симулятор заточки Archeage"/>
  <meta name = "keywords" content = "Симулятор, шансы, шансы заточки, улучшение снаряжения, зачарование" />
<script src="https://yandex.st/jquery/1.7.2/jquery.min.js"></script>
<?php echo $script; ?>
<title>Симулятор заточки</title>
 <link href="css/defolt.css?ver=12" rel="stylesheet">
 <!--<link href="css/packstable.css?ver=123" rel="stylesheet">-->
 <!--<link href="css/small_window.css?ver=1" rel="stylesheet">-->
 <link href="css/enchant.css?ver=12" rel="stylesheet">
</head>

<body>

<?php
include 'pageb/header.html';
	//preg_match('/выражение/',$n)
?>
<div class="topw"></div>
<div class="input1"><div class="input2"><div class="inpur">
<div class="winhead"></div>
<!--<div class="mess">
<?php // echo $mess;?>
</div>-->
<form method="POST" action="enchant.php" id="search_form" name="search_form">
<div class="top_login">
<div class="top_menu">
<div class="win_name">Улучшение 
<select class="item_type" name='group' autocomplete="off" onchange="this.form.submit()">
<option value="1" <?php echo $group_1;?>>крафтового</option>
<option value="2" <?php echo $group_2;?>>обсидиана и РБ</option>
<!--<option value="3">бижутерии</option>-->
	</select>
</div>
<div class="win_name">
	<select name="grade" class="item_type" autocomplete="off" onchange="this.form.submit()" style="<?php echo 'color: '.$bord_color;?>;">
	<?php
		echo implode(',',$grade_list);
		?>
	
	</select>
</div>
</div>
<div class="login"><?php echo $fname.$avatar;?></div>
	</div>
<div class="main_area">
<div class="main_row"></div>
<div class="item_row">
<div class="item_icon" id="item_icon" style="border-color: <?php echo $bord_color;?>;" >
<?php 
	if($item_id>0 and $item_q != '')
	echo
'<a href="catalog.php?query_id='.$item_id.'&query='.$item_q.'" target="_blank">
<div class="itemprompt" data-title="По '.round($item_pr/10000).'gold.">'
.$item_icon.'</div></a>';?></div></div>
<div class="item">
<div class="item_name_row">
	<input name="query" type="search" id="search_box" class="item_sel" placeholder="Имя предмета..." autofocus autocomplete="off" onchange="submit" value="<?php echo $item_q;?>">
	<input type="hidden" id="search_box2" name="query_id" value="<?php echo $item_id;?>">
	<input type="hidden" name="old_group" value="<?php echo $group;?>"/>
	<input type="hidden" name="old_item" value="<?php echo $item_id;?>"/>
		<div id="search_advice_wrapper"></div>
	</div><div class="mid_row">
	<div class="smile">
	<?php
		echo $result_mess.$smile;
		?>
    </div></div>	
<div class="enchant_row"><div class="roll_part"><div class="roll_icon">
<?php 
	if($roll_id>0 and $roll_name != '')
	echo
'<a href="catalog.php?query_id='.$roll_id.'&query='.$roll_name.'" target="_blank">
<div class="itemprompt" data-title="По '.round($roll_pr/10000).'gold.">'
.$roll_icon.'</div></a>';?></div>
</div>
<div class="enchant_mid"></div>
<div class="catal_part"><div class="catal_icon">
<?php 
	if($catal>0 and $catal_name != '')
	echo
'<a href="catalog.php?query_id='.$catal.'&query='.$catal_name.'" target="_blank">
<div class="itemprompt" data-title="По '.round($catal_pr/10000).'gold.">'
.$catal_icon.'</div></a>';
?>
</div></div>
</div>
<div class="enchant_row2">
    <div class="itemprompt" data-title="Двойные не могу расчитать, пока не будет достаточно данных о ценах клика.">
	<select class="roll" name ="roll" onchange="this.form.submit()">
	<option value="1" <?php echo $sel_roll_1;?>>Простой</option>
	<!--<option value="2" <?//php echo $sel_roll_2;?>>Двойной</option>-->	
	</select></div>
	<div class="enchant_mid2"></div>
	<select class="catal" name="catal" autocomplete="off" onchange="this.form.submit()">
	<?php
	echo implode(',',$catals);	
	?>	
		
	</select>
</div></div></div>

<?php
	$reset = false;
	if(isset($_POST['reset']) and $_POST['reset'] == 'Отмена') $reset = true;
	if($item_id > 0 and $grade < 12 and !$reset)
	include 'functions/ench_form_2.php';
	?>
	</form>

<div class="mess">
<details>
Симулятор находится в стадии разработки. В конечном виде он должен уметь самостоятельно перебирать разные маршруты улучшения и выбирать оптимальный.<br> Чтобы продвинуться дальше нужны данные о стоимостях клика. Сохраняйте, пожалуйста, верные и точные (включая серебро и медь) данные.<br>
Если вы компетентны в области математики или программирования и можете оказать помощь или дать совет, присоединяйтесь пожалуйста к обсуждениям:<br>
<a href="https://aa.mail.ru/forums/showthread.php?t=275759" style="color: #6C3F00; text-decoration: none;"><b>О ценах клика</b></a><br>
<a href="https://aa.mail.ru/forums/showthread.php?t=275676" style="color: #6C3F00; text-decoration: none;"><b>О симуляторе</b></a><br>
<div class="itemprompt" data-title="Есть надежда, что анализ этих данных позволит генерировать это значение.">
<a href="https://dllib.ru/ench_users_cost.php" style="color: #6C3F00; text-decoration: none;"><b>Сводка собранных цен клика</b></a></div><br>

</details>
</div>
</div>
</div>

</div>

<?php
	
	include 'pageb/footer.php';
	?>
	
</body>
</html>