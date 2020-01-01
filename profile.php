<?php 
include_once 'includs/usercheck.php';
//include_once 'includs/user.php';
$hrefself = '<a href="'.$_SERVER['PHP_SELF'].'?query=';
$ver = random_str(8);
//$token = random_str(8);
$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
		die('error_sess');
if(!$email) exit;
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Профиль." />
  <meta name=“robots” content=“noindex, nofollow”>
<title>Профиль</title>
<link href="css/default.css?ver=<?php echo md5_file('css/default.css')?>" rel="stylesheet">
<link href="css/profile.css?ver=<?php echo md5_file('css/profile.css')?>" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="token" content="<?php echo SetToken()?>">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
<?php if(!$ismobiledevice)
{
	?><script type="text/javascript" src="js/tooltips.js?ver=<?php echo md5_file('js/tooltips.js')?>"></script><?php
}
?>
</head>

<body>

<?php include_once 'includs/header.php';
$query = qwe("
SELECT
`sessions`.`sess_id`,
`sessions`.`device_type`,
`mailusers`.`first_name`,
`mailusers`.`last_name`,
`mailusers`.`mailnick`,
`mailusers`.`avatar`,
`mailusers`.`avafile`,
`mailusers`.`mail_id`,
`mailusers`.`last_time`,
(`mailusers`.`mail_id` = '$user_id') as `active`,
`mailusers`.`user_nick` as `nick`,
`mailusers`.`identy` as `midenty`
FROM
`sessions`
INNER JOIN `mailusers` ON `sessions`.`user_id` = `mailusers`.`mail_id`
WHERE
`sessions`.`sessmark` = '$sessmark'
AND `mailusers`.`email` IS NOT NULL
ORDER BY `active` DESC, `last_time` DESC
");	  
?>
<main>
<div id="rent">
	<div class="menu_area">
	<h2>Ваши акаунты</h2>
	<?php
	if($query and $query->num_rows >1)
	{
		?><p>Переключайтесь между акаунтами, кликая по аватарке</p><?php
	}

	?>
	<hr>
	</div>
<div id="rent_in" class="rent_in">
<div class="clear"></div>
<div class="all_info" id="all_info">
<div id="items"><div id="accs">
<form method="post" action="edit/accahge.php">
<?php


$checks = ['','checked'];
foreach($query as $q)
{
	extract($q);
	if(!$nick) 
		$nick = NickAdder($mail_id);
	
	if(!$avafile)
		$avafile = UserInfo($midenty)['avatar'];
	else 
		$avafile = 'img/avatars/'.$avafile;
	
	if(!file_exists($avafile)) 
		$avafile = '/img/icons/8001096.png';
	
	$check = $checks[$active];
	?>
	<div class="persrow">
		
		<div class="nicon_out">
			<input type="radio" id="<?php echo $mail_id?>" name="muser" value="<?php echo $mail_id?>" <?php echo $check?> onchange="this.form.submit()">
			<label class="navicon" for="<?php echo $mail_id?>" style="background-image: url(<?php echo $avafile?>);"></label>
			<div class="persnames">
				<div class="first_name"><?php echo $first_name?></div>
				<div class="last_name"><?php echo $last_name?></div>
				<div class="mailnick"><?php echo $mailnick?></div>	
			</div>
		</div>
		<?php 
		if($active)
		{
			?><a href="exit.php"><button type="button" class="def_button">Выход</button></a><?php
		}
		?>
	</div>
	<span>Публичный ник:<br></span>
	<?php if($active)
		{
		?>
		
		<input data-tooltip="Публичный ник.<br>Только буквы и цифры<br>3-20 символов." id="public_nick" type="text" name="public_nick" placeholder="Публичный ник" value="<?php echo $user_nick?>" autocomplete="off" />

		<button type="button" id="sendnick" class="def_button" style="display: none;">Ok</button>
		<span style="color: green; font-size: 20px;" id="Nick_Ok"></span>
		<?php
		}else
		{
			echo '<b>'.$nick.'</b>';
		}
		?>
	
	<hr>
	<?php
}

?>



</form>


<br>
<details><summary>Как добавить аккаунт?</summary><br>
<ul>
	<li>Нажмите "Выход"</li>
	<li>На mail.ru (или aa.mail.ru) войдите в аккаунт, который хотите добавить</li>
	<li>Выполните вход на dllib.ru</li>
</ul>
</details>
</div>
</div>
</div>
</div></div>
</main>
<?php 
include_once 'pageb/footer.php'; ?>
</body>
<script type='text/javascript'>
	
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
	
function NickValid(nick)
{
	var re = new RegExp("^([a-zA-Zа-яА-ЯёЁ0-9]{3,20})$");
	var re1 = new RegExp("^([a-zA-Z0-9]{3,20})$");
	var re2 = new RegExp("^([а-яА-ЯёЁ0-9]{3,20})$");
	var okid = "#Nick_Ok";
	
	if (re1.test(nick) || re2.test(nick)) {
	
	$('#sendnick').show();
	//console.log("Valid");
		return true;
	} else {
		if(re.test(nick))
		{
			console.log("mixed");
			$(okid).prop('style','color: red');
			$(okid).html('Не смешивайте языки');
			$(okid).show();
			setTimeout(function() {$(okid).hide('slow');}, 1000);
		}
		$('#sendnick').hide();
		console.log("Invalid");
		return false;
	}
}

function SetNick(nick)
{ 
	var okid = "#Nick_Ok";

	$.ajax
	({
		url: "hendlers/setnick.php", // путь к ajax файлу
		type: "POST",      // тип запроса
		dataType: "html",
		headers: {
            'X-CSRF-TOKEN': $('meta[name="token"]').attr('content')
        },
		cache: false,
		data: {
			nick: nick
		},
		// Данные пришли
		success: function(data) 
		{
			if(data != 'ok')
				$(okid).prop('style','color: red');
			else 
				$(okid).prop('style','color: green');
			
			if(data == 'reload')
				{
					$(okid).html("Ой, не понял! Еще разик, плз.");
					$(okid).show();
					setTimeout(function() {$(okid).hide('slow');}, 1000);
					return document.location.reload(true);
					
				}
				
			$('#sendnick').hide();
			$(okid).html(data);
			$(okid).show();
			setTimeout(function() {$(okid).hide('slow');}, 1000);
		}
	});
}
</script>
</html>