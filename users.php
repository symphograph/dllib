<?php 
include_once 'includs/usercheck.php';
setcookie('path', 'users');

$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);

?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Калькулятор себестоимости ресурсов Archeage." />
  <meta name = "keywords" content = "Умный калькулятор, archeage, архейдж, крафт" />
  <meta name=“robots” content=“index, nofollow”>
<title>Пользователи</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/users.css?ver=<?php echo md5_file('css/users.css')?>" rel="stylesheet">
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
$qwe = qwe("
SELECT 
`mail_id`, 
`cnt`, 
`first_name`, 
`last_name`,
`last_time`, 
`email`, 
`user_nick`, 
`avatar`,
`avafile`, 
`mtime`, 
(folows.folow_id > 0) as `isfolow`,
`identy` as `midenty`,
flwt.flws
FROM
(SELECT `user_id`, COUNT(*) as `cnt`, max(`time`) as `mtime` FROM `prices`
WHERE `server_group` = '$server_group' /*AND `user_id` != '$user_id'*/
AND `item_id` NOT in (".implode(',',IntimItems()).")
GROUP BY `user_id`
ORDER BY `time` DESC
) as `tmp`
INNER JOIN `mailusers` ON `mailusers`.`mail_id` = `tmp`.`user_id`
AND `mailusers`.`email` LIKE '%@%' /*AND tmp.`cnt` > 2*/
LEFT JOIN `folows` ON folow_id = `mail_id` AND `folows`.`user_id` = '$user_id'
LEFT JOIN (SELECT count(*) as flws, user_id, folow_id  FROM `folows` GROUP BY folow_id) as flwt 
ON `mail_id` = flwt.folow_id
ORDER BY `isfolow` DESC, YEAR(`mtime`) DESC, MONTH(`mtime`) DESC, WEEK(`mtime`,1) DESC, (cnt>50) DESC, `mtime` DESC
LIMIT 100
");	  
?>
<main>
<div id="rent">
	<div class="menu_area">
	<div class="navcustoms">
	<h2>Цены пользователей</h2>
		<div class="buttons">

			<form method="POST" action="serverchange.php" name="server">
				<select name="serv" id="server" class="server" onchange="this.form.submit()">
				<?php
				$query = qwe("SELECT * FROM `servers`");
				SelectOpts($query, 'id', 'server_name', $server, false);	

				?>
				</select>
			</form>
			<a href="user_prices.php"><button class="def_button">Мои цены</button></a>
			<a href="user_customs.php"><button class="def_button">Настройки</button></a>
		</div>
	</div><hr>
	</div>
<div id="rent_in" class="rent_in">
<div class="clear"></div>
<div class="all_info" id="all_info">
<div id="items">
	<details><summary><b>Как это работает?</b></summary>
		<div class="long_text"><br>
		В обычном режиме калькулятор ищет цены, предпочитая Ваши записи.
		Если их нет, используются цены других пользователей.
		В этом случае можно получить неожиданный результат.<br>
		Здесь Вы можете выбрать пользователей, чьим ценам Вы доверяете.
		В настройках можно выбрать желаемую область видимости цен.<br><br>
		
		Если цена пользователя, которому Вы доверяете, новее Вашей, она будет использована в расчетах.<br><br>
		Эта опция не распространяется на предметы, имеющие цену субъективного характера. Например, Ремесленнаую репутацию, Очки работы, Честь, Вексель региональной общины и еще пару сотен подобных. При любых настройках из этого списка будет предпочитаться именно Ваша цена.
		<br><br>
		Ники сгенерированы случайным образом. 
		<?php
		if($email)
			echo 'Изменить свой ник можно в <a href="profile.php">профиле</a>.';
		?>			
		</div>
	</details><br>
<form method="post" id="fol_form">
<?php

//var_dump($device_type);
$checks = ['','checked'];
foreach($qwe as $q)
{
	$chk = '';
	extract($q);
	if($isfolow) $chk = 'checked';

	if(!$avafile)
		$avafile = UserInfo($midenty)['avatar'];
	else 
		$avafile = 'img/avatars/'.$avafile;
	
	if(!file_exists($avafile)) 
		$avafile = '/img/init_ava.png';
	if(!$user_nick) 
		$user_nick = NickAdder($mail_id);
	//var_dump($server_group);
	?>
	<div class="persrow">
		
		<div class="nicon_out">
			
			<a href="user_prices.php?puser_id=<?php echo $mail_id?>" data-tooltip="Смотреть цены">
			<label class="navicon" for="<?php echo $mail_id?>" style="background-image: url(<?php echo $avafile?>);"></label>
			</a>
			<div class="persnames">
				<div class="mailnick"><b><?php echo $user_nick?></b></div>
				<div class="mailnick"><?php echo 'Записей: '.$cnt?></div>
			</div>
		</div>
		<div class="lastprice">
			<div class="mailnick"><?php echo 'Последняя: '.date('d.m.Y',strtotime($mtime)) ?>	
			<?php LastUserPriceCell($mail_id,$server_group);?>
			</div>
		</div>
		<div class="folow_check">
		<?php 
		if($mail_id != $user_id)
		{
			?>
			<label for="folw_<?php echo $mail_id?>">Доверять ценам
				<input type="checkbox" <?php echo $chk?> name="folow[<?php echo $mail_id?>]" id="folw_<?php echo $mail_id?>" value="1">
			</label>
			<?php
		}
		?>
		<div class="mailnick"><?php if($flws) echo 'Доверяют: '.$flws?></div>
		</div>
	</div>
	<hr>
<?php
}
?>

<input type="hidden" name="folow[0]" value=0>
</form>
</div>
</div>
</div></div>
</main>
<?php 
include_once 'pageb/footer.php'; ?>
</body>
<?php
function LastUserPriceCell($mail_id,$server_group)
{
	$qwe = qwe("
	SELECT 
	`prices`.`auc_price`,
	`prices`.`item_id`,
	`items`.`item_name`,
	`items`.`icon`,
	`items`.`basic_grade`
	FROM `prices` 
	INNER JOIN `items` ON `items`.`item_id` = `prices`.`item_id`
	AND `prices`.`user_id` = '$mail_id'
	AND `prices`.`server_group` = '$server_group'
	AND `prices`.`item_id` NOT in (".implode(',',IntimItems()).")
	ORDER BY `time` DESC 
	LIMIT 1
	");
	if(!$qwe or $qwe->num_rows == 0)
		return false;
	$qwe = mysqli_fetch_assoc($qwe);
	extract($qwe);
	PriceCell2($item_id,$auc_price,$item_name,$icon,$basic_grade);
}
?>
<script type='text/javascript'>
$('#fol_form').on('change','input[type="checkbox"]',function(){
	 
	var form = $('#fol_form');
	$.ajax
	({
		url: "hendlers/setfolow.php", // путь к ajax файлу
		type: "POST",      // тип запроса
		dataType: "html",
		cache: false,
		data: form.serialize(),
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
$('#all_info').on('click','#sendnick',function(){
	var nick = $('#public_nick').val();
	var valid = NickValid(nick);
	if(valid)
	SetNick(nick);
});
	
$('#all_info').on('input','#public_nick',function(){
	var nick = $('#public_nick').val();
	NickValid(nick);
});
</script>
</html>