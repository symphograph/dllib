<?php
$item_icon = '';
$item_q = '';
$item_id = 0;
$roll_icon = '';
$catal_icon = '';
$delvl_ch = 0;
$brake_ch = 100;
$save = 0;
$click_cost = 0;
$mail_id = 1;
$item_group = 0;
$catal_name = '';
$catal = 0;
$catal_pr = 0;
$real_cost = 0;
$roll_pr = 0;
$chance = 0;
$item_pr = 0;
$rand_brake = 0;
$cost = 0;
$newitem = false;
if(isset($_COOKIE['real_cost']) and ctype_digit($_COOKIE['real_cost'])) $real_cost = $_COOKIE['real_cost'];
$try_start = false;
if(isset($_POST['chance']) and ctype_digit($_POST['chance'])) $chance = $_POST['chance'];
if(isset($_POST['try_start']) and $_POST['try_start'] == 'Ok')
	$try_start = true;
$rand = 0;
if($try_start) $rand = rand(0,1000000);
///Проверка на успех попытки
if($rand > 0) 
		{if($rand <= $chance)$success = true; else $success = false;}
$chance = round($chance/10000,2);
$auc_craft = 1; $a_cr_sel_1 = 'checked'; $a_cr_sel_2 = '';
$roll_name = ''; $roll_id = 0;
$mess = 'Привет. Я кальклятор Графа. На форуме он Lastor. Я способен расчитать все шаги заточки и подсказать рациональный выор на каждом из них. Но я не знаю стоимости кликов и некоторых шансов. Сообщи мне эти данные и я смогу это сделать. Сейчас я не могу симулировать заточку. Но если буду знать стоимость клика, то всё получится. Сообщи стоимость (внизу появится, когда выберешь предмет). Граф разберётся и, если Нуя позволит, напишет толковый симулятор.<a href="enchant.php?mess=1" style="color: #6C3F00; text-decoration: none;"><br>Убрать сообщение</a>';
if(isset($_GET['mess']) and $_GET['mess'] == 1) {$mess = '<a href="enchant.php?mess=2" style="color: #6C3F00; text-decoration: none;">Я тут говорил... Нажми, если забыл.</a>'; setcookie('mess', '2', time()+3600*24, "/");}
if(isset($_COOKIE['mess']) and ($_COOKIE['mess'] == 2)) $mess = '<a href="enchant.php?mess=2" style="color: #6C3F00; text-decoration: none;">Показать сообщение</a>';
if(isset($_GET['mess']) and $_GET['mess'] == 2) {setcookie('mess', '1', time()+3600*24, "/"); $mess = 'Привет. Я кальклятор Графа. На форуме он Lastor. Я способен расчитать все шаги заточки и подсказать рациональный выор на каждом из них. Но я не знаю стоимости кликов и некоторых шансов. Сообщи мне эти данные и я смогу это сделать. Сейчас я не могу симулировать заточку. Но если буду знать стоимость клика, то всё получится. Сообщи стоимость (внизу появится, когда выберешь предмет). Граф разберётся и, если Нуя позволит, напишет толковый симулятор.<a href="enchant.php?mess=1" style="color: #6C3F00; text-decoration: none;"><br>Убрать сообщение</a>';}

//авторизация
if(isset($_GET['exit']))
	{
    setcookie('fname', '');
	setcookie('mailid', '');
	setcookie('avatar', '');
		echo '<meta http-equiv="refresh" content="0; url=enchant.php">';
	}
if(isset($_COOKIE['mailid']) and ctype_digit($_COOKIE['mailid']) and $_COOKIE['mailid']<1000000)
	$mail_id = $_COOKIE['mailid'];
$avatar = '';
	if(isset($_COOKIE['avatar']) and $mail_id > 1 and $mail_id <1000000)
	$avatar = '<img src="'.$_COOKIE['avatar'].'" width="50" height="50" alt="avatar"/><br>
	<a href="enchant.php?exit=1" style="color: #6C3F00; text-decoration: none;">Выйти</a>';
$fname  = '<a href="oauth/mailru.php?path=enchant" style="color: #6C3F00; text-decoration: none;"><b>Войдите</b></a>, чтобы ваши <br>настройки сохранялись.';
if(isset($_COOKIE['fname']) and $mail_id > 1 and $mail_id <1000000)
	$fname = 'Привет, '.mysqli_real_escape_string($dbLink,$_COOKIE['fname']).'! ';
$try_start = false;
if(isset($_POST['try_start']) and $_POST['try_start'] == 'Ok')
	$try_start = true;

if(isset($_POST['auc_craft']) and ctype_digit($_POST['auc_craft']))
	$auc_craft = $_POST['auc_craft'];
if(isset($_POST['query'])) $item_q = mysqli_real_escape_string($dbLink,trim($_POST['query']));
if(isset($_POST['query_id']) and ctype_digit($_POST['query_id']) and $_POST['query_id'] >0)
 {$item_id = $_POST['query_id'];
$item_icon = '<img src="img/icons/'.$item_id.'.png" width=46px height=46px/>';
  if(isset($_POST['old_item']) and $_POST['old_item'] !=$item_id) $newitem = true;
 }
$group = 1; $group_1 = 'selected'; $group_2 = ''; $chance_group = 'chance_craft';
if(isset($_POST['group']) and ctype_digit($_POST['group']))
	$group = $_POST['group'];
if(isset($_GET['gr']) and ctype_digit($_GET['gr']))
	$group = $_GET['gr'];
$old_group = 1;
if(isset($_POST['old_group']) and ctype_digit($_POST['old_group']))
	$old_group = $_POST['old_group'];
if($old_group != $group and isset($_POST['group']))
	{echo '<meta http-equiv="refresh" content="0; url=enchant.php?gr='.$group.'">'; exit();}
$script = '<script type="text/javascript" src="js/roll_group_'.$group.'.js?ver=2"></script>';
if($group == 2) {$group_1 = ''; $group_2 = 'selected'; $chance_group = 'chance_obsid';}
$roll = 1; $sel_roll_1 = 'selected'; $sel_roll_2 = '';
if(isset($_POST['roll']) and ctype_digit($_POST['roll']))
	$roll = $_POST['roll'];
if($roll == 2) {$sel_roll_1 = ''; $sel_roll_2 = 'selected';}
$catal = 0; $cat_sel = '';
if(isset($_POST['catal']) and ctype_digit($_POST['catal']))
{$catal = $_POST['catal'];
$catal_icon = '<img src="img/icons/'.$catal.'.png" width=46px height=46px/>';
}
//категория итема
if($item_id > 0)
{
		$query = qwe("SELECT `item_categories`.`item_group` 
		FROM `items`, `item_categories` 
		WHERE `items`.`item_id` = '$item_id' 
		AND `item_categories`.`id` = `items`.`categ_id`");	
		foreach($query as $v)
		{
			$item_group = $v['item_group'];
		}

	//Свитки
	$query = qwe("SELECT * FROM `rolls` WHERE `item_group` = '$item_group' AND `double_up` = '$roll'");
		foreach($query as $v)
		{
			$roll_id = $v['roll_id'];
			$roll_name = $v['roll_name'];
		}
	$roll_icon = '<img src="img/icons/'.$roll_id.'.png" width=46px height=46px/>';
	
	$gold = $silver = $bronze = 0;
if(isset($_POST['save_params']) and $_POST['save_params'] == 'Сохранить')
{
if(isset($_POST['roll_gold']) and ctype_digit($_POST['roll_gold']))
	$gold = $_POST['roll_gold'];
if(isset($_POST['roll_silver']) and ctype_digit($_POST['roll_silver']))
	$silver = $_POST['roll_silver'];
if(isset($_POST['roll_bronze']) and ctype_digit($_POST['roll_bronze']))
	$bronze = $_POST['roll_bronze'];
}
$roll_pr = $gold*10000+$silver*100+$bronze;
	if($roll_pr > 0)
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `auc_price`, `time`) 
	VALUES ('$mail_id', '$roll_id', '$roll_pr', now())");
	else
	{
	$query = qwe("SELECT * FROM `user_crafts` WHERE `item_id` = '$roll_id' AND `isbest` > 0 AND `isbest` < 3 ORDER BY `updated` DESC");
	$froll_pr = $roll_pr = $fcraft_pr = $roll_craft_pr = 0;

	foreach($query as $v)
	{
		if($fcraft_pr == 0) 
		{
			$fcraft_pr = $v['craft_price'];
		}
		if($mail_id == $v['user_id'])
			{$roll_craft_pr = $v['craft_price']; break;}
	}

	if($roll_craft_pr == 0) $roll_craft_pr = $fcraft_pr;
	if($roll_craft_pr == 0 or $auc_craft == 1)
	{
		$query = qwe("SELECT * FROM `prices` WHERE `item_id` = '$roll_id' ORDER BY `time` DESC");
		foreach($query as $v)
		{
			if($froll_pr == 0) $froll_pr = $v['auc_price'];
			if($mail_id == $v['user_id']) 
				{$roll_pr = $v['auc_price']; break;}
		}
		if($roll_pr == 0) $roll_pr = $froll_pr;
	}
	if($auc_craft == 2) 
		{$a_cr_sel_1 = ''; $a_cr_sel_2 = 'checked';
			if($roll_craft_pr > 0) $roll_pr = $roll_craft_pr;
		
		}
	if($auc_craft == 1)
	  {   
	    if($roll_pr == 0) 
		$roll_pr = $roll_craft_pr;
		}
	}
}
//Грейды
$grade = 1;

	if(isset($_POST['grade']) and ctype_digit($_POST['grade']))
	{
		$grade = $_POST['grade'];
		if($rand > 0)
		{
			if($success) $grade = $grade+1;
		}
		$guery = qwe("SELECT * FROM `grades` WHERE `id` = '$grade'");
		foreach($guery as $v)
		{
			$color = $v['color'];
		}
	}
	$query_gr = qwe("SELECT * FROM `grades`");
$gr_sel = ''; $grade_list = array();
		foreach($query_gr as $v)
		{
			$gr_id = $v['id'];
			$gr_name = $v['gr_name'];
			$color = $v['color'];
			if($gr_id == $grade) 
			{
				$gr_sel = 'selected'; $bord_color = $color;
				$chance = $v[$chance_group]/100;
			
			}
			$grade_list[] = '<option value="'.$gr_id.'" style="color: '.$color.';" '.$gr_sel.'>'.$gr_name.'</option>';
			$gr_sel = '';
		}

//Катализаторы
$supp_mul = 0;
			$query = qwe("SELECT * from `catals` 
			WHERE (`grade_min` <= '$grade' AND `grade_max` >= '$grade') 
			AND (`equip_type` = 100 OR `equip_type` = '$item_group')");
			$catals = array();
		foreach($query as $v)
			{
				$cat_id = $v['item_id'];
				$cat_name = $v['item_name'];
				$sus_mul = $v['add_success_mul'];
				if($cat_id == $catal) 
					{
					$cat_sel='selected';
						$supp_mul = $v['add_success_mul'];
						$save = $v['save'];
						$catal_name = $cat_name;
					}
				$catals[] = '<option value="'.$cat_id.'" '.$cat_sel.'>'.$cat_name.'</option>,';
				$cat_sel='';
			}
			$supp_mul = $supp_mul/100+1;
			$chance2 = $chance*$supp_mul;
			if($chance2 > 100) $chance2 = 100;



$brake = $dlvl = false;
if($grade == 7)
{	
	$brch = 80; $dlvlch = 80;
	if(isset($_COOKIE['brake_ch'])) $brch = $_COOKIE['brake_ch'];
	if(isset($_COOKIE['dlvlch'])) $dlvlch = $_COOKIE['dlvlch'];
	if(isset($_POST['brake_ch']) and ctype_digit($_POST['brake_ch']))
		{$brch = $_POST['brake_ch']; setcookie('brake_ch', $brch, time()+3600*24*360, "/");}
	if(isset($_POST['dlvlch']) and ctype_digit($_POST['dlvlch'])) 
		{$dlvlch = $_POST['dlvlch'];	setcookie('dlvlch', $dlvlch, time()+3600*24*360, "/");}
}
if($grade > 7) {$brch = 90; $dlvlch = 30;}
if($rand > 0 and $save == 0 and $grade > 6 and !$success)
{	
	if($brch > 0)
	{	
	$rand_brake = rand(1,100);
		if($rand_brake <= $brch) $brake = true;
	}
	if(!$brake and $dlvlch >0)
	{
		$rand_dlvl = rand(1,100);
		if($rand_dlvl <= $dlvlch) $dlvl = true;
	}
}
///Если грейд понизался, крутим цикл грейдов снова
if($dlvl)
{   $grade = 4;
	$gr_sel = ''; $grade_list = array();
		foreach($query_gr as $v)
		{
			$gr_id = $v['id'];
			$gr_name = $v['gr_name'];
			$color = $v['color'];
			if($gr_id == $grade) 
			{
				$gr_sel = 'selected'; $bord_color = $color;
				$chance = $v[$chance_group]/100;
			
			}
			$grade_list[] = '<option value="'.$gr_id.'" style="color: '.$color.';" '.$gr_sel.'>'.$gr_name.'</option>';
			$gr_sel = '';
		}
}
if($grade == 7) {$brake_ch = '<div class="itemprompt" data-title="А я откуда знаю?"><input name="brake_ch" type="number" min="0" max = "100" value="'.$brch.'" class="roll user_chance" autocomplete="off" /></div>'; $delvl_ch = '<div class="itemprompt" data-title="А я откуда знаю?"><input name="dlvlch" type="number" min="0" max = "100" value="'.$dlvlch.'" class="roll user_chance" autocomplete="off"/></div>';}
if($save == 1 or $grade < 7) {$brake_ch = $delvl_ch = 0;}
//Цена клика
$gold = $silver = $bronze = 0;
if($item_id > 0)
{	
	if(isset($_POST['save_params']) and $_POST['save_params'] == 'Сохранить')
	{
	if(isset($_POST['click_gold']) and ctype_digit($_POST['click_gold']))
		$gold = $_POST['click_gold'];
	if(isset($_POST['click_silver']) and ctype_digit($_POST['click_silver']))
		$silver = $_POST['click_silver'];
	if(isset($_POST['click_bronze']) and ctype_digit($_POST['click_bronze']))
		$bronze = $_POST['click_bronze'];
	}
	$cost = $gold*10000+$silver*100+$bronze;
	if($cost >0) 
		qwe("REPLACE INTO `enchant_cost` 
	(`mail_id`, `item_id`, `grade`, `cost`, `time`) 
	VALUES ('$mail_id', '$item_id', '$grade', '$cost', now())");
	else 
	{	$fcost = 0;
		$query = qwe("SELECT * FROM `enchant_cost` WHERE `grade` = '$grade' AND `item_id` = '$item_id' ORDER BY `time` DESC");
			foreach($query as $v)
			{   if($fcost == 0) $fcost = $v['cost'];
				if($mail_id == $v['mail_id'] and $mail_id>1)
				{$cost = $v['cost']; break;}
			}
		if($cost <1) $cost = $fcost;

		}
}
//Катализатор
$catal_pr = 0; $fcatal_pr = 0;
$gold = $silver = $bronze = 0;
if($catal > 0)
{
	if(isset($_POST['save_params']) and $_POST['save_params'] == 'Сохранить')
	{
	if(isset($_POST['catal_gold']) and ctype_digit($_POST['catal_gold']))
		$gold = $_POST['catal_gold'];
	if(isset($_POST['catal_silver']) and ctype_digit($_POST['catal_silver']))
		$silver = $_POST['catal_silver'];
	if(isset($_POST['catal_bronze']) and ctype_digit($_POST['catal_bronze']))
		$bronze = $_POST['catal_bronze'];
	}
	$catal_pr = $gold*10000+$silver*100+$bronze;
}

if($catal_pr > 0) 
	qwe("REPLACE INTO `prices` (`user_id`, `item_id`, `auc_price`, `time`) 
	VALUES ('$mail_id', '$catal', '$catal_pr', now())");


if($catal >0 and $catal_pr < 1)
{	
	$query = qwe("SELECT * FROM `prices` WHERE `item_id` = '$catal' ORDER BY `time` DESC");
	foreach($query as $v)

	{   
		if($fcatal_pr == 0) $fcatal_pr = $v['auc_price'];
		if($mail_id = $v['user_id'] and $mail_id>1)
		{$catal_pr = $v['auc_price']; break;}
	}
	if($catal_pr < 1) $catal_pr = $fcatal_pr;
}
if($catal >0 and $catal_pr < 1)
{
	$query = qwe("SELECT * FROM `user_crafts` WHERE `item_id` = '$catal' ORDER BY `updated` DESC");
	foreach($query as $v)
	{
		if($fcatal_pr == 0) $fcatal_pr = $v['craft_price'];
	if($mail_id = $v['user_id'] and $mail_id>1)
	{$catal_pr = $v['craft_price']; break;}
	}
	if($catal_pr < 1) $catal_pr = $fcatal_pr;
}

//Цена итема
$item_pr = 0; 
$gold = $silver = $bronze = 0;
if($item_id > 0 and $grade > 6)
{
	if(isset($_POST['save_params']) and $_POST['save_params'] == 'Сохранить')
	{
	if(isset($_POST['item_gold']) and ctype_digit($_POST['item_gold']))
		$gold = $_POST['item_gold'];
	if(isset($_POST['item_silver']) and ctype_digit($_POST['item_silver']))
		$silver = $_POST['item_silver'];
	if(isset($_POST['item_bronze']) and ctype_digit($_POST['item_bronze']))
		$bronze = $_POST['item_bronze'];
	}
	$item_pr = $gold*10000+$silver*100+$bronze;
}

if($item_pr > 0 and isset($_POST['save_params']) and $_POST['save_params'] == 'Сохранить') 
	qwe("REPLACE INTO `ench_it_prices` (`user_id`, `item_id`, `grade`, `price`, `time`) 
	VALUES ('$mail_id', '$item_id', '$grade', '$item_pr', now())");
else 
	if($grade > 6)
{
	$fitem_pr = 0;
	$query = qwe("SELECT * FROM  `ench_it_prices` 
	WHERE `item_id` = '$item_id' 
	AND `grade` = '$grade' 
	ORDER BY `time` DESC ");
 foreach($query as $v)
	{
	if($fitem_pr == 0) $fitem_pr = $v['price'];
	if($mail_id = $v['user_id'] and $mail_id>1)
	{$item_pr = $v['price']; break;}
	}
	if($item_pr < 1) $item_pr = $fitem_pr;
}

$regrade = 0;
$result_mess = "";
if($rand == 0)
{if($chance2 < 100)
	
$smile = '
	<img src="http://www.kolobok.us/smiles/standart/superstition.gif" alt="Будем пробовать" border="0">';
if($chance2 == 100)
	$smile = '
	<img src="http://www.kolobok.us/smiles/standart/smoke.gif" alt="Точим безопасно" border="0">';
}
else
{
if($success)
	{$result_mess = "Грейд повысился<br>";
	$smile = '<img src="http://www.kolobok.us/smiles/standart/victory.gif" alt="Успех" border="0">';
	}
	else
	{	if(!$brake and !$dlvl)
		{$result_mess = "Зачарование не удалось.<br>Качество осталось прежним.<br>";
	$smile = '<img src="http://www.kolobok.us/smiles/standart/nea.gif" alt="Неудача" border="0">';}
		 
	 if($brake)
		 {$result_mess = "Зачарование не удалось.<br>Предмет уничтожен.<br>";
		 $smile = '<img src="http://www.kolobok.us/smiles/standart/cray2.gif" alt="Предмет уничтожен" border="0">';
		 }
	 if($dlvl)
	 {
		{$result_mess = "Зачарование не удалось.<br>Качество снижено.<br>";
		 $smile = '<img src="http://www.kolobok.us/smiles/standart/cray2.gif" alt="Предмет уничтожен" border="0">';
		 $regrade = 4;
		 } 
	 }
	}
}
if($grade < 12)
{	if($save == 1) $item_pr2 = 0; else $item_pr2 = $item_pr;
	$click_cost = $cost+$catal_pr+$roll_pr+$item_pr2;
	$trys = 100/$chance2;
	$click_all_cost = round($click_cost*$trys);
	if($rand > 0)
	{$real_cost = round($click_cost+$real_cost);
	if($real_cost > 0)
	setcookie('real_cost', $real_cost, time()+3600*24*360, "/");
	}
	
		   if(isset($_POST['reset']) or isset($_GET['gr']) or $newitem)
		   {$real_cost = 0; setcookie('real_cost', '');}
}
?>