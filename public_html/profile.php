<?php
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
$User = new User;
$User->check();
$user_id = $User->id;

$ver = random_str(8);

$sessmark = OnlyText($_COOKIE['sessmark']);	
	if(iconv_strlen($sessmark) != 12)
		die('error_sess');
if(!$User->email) exit;
?>
<!doctype html>
<html lang="ru">
<head>

<meta charset="utf-8">
<meta name = "description" content = "Профиль." />
  <meta name=“robots” content=“noindex, nofollow”>
<title>Профиль</title>
    <?php CssMeta(['default.css','profile.css']);?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="token" content="<?php echo SetToken()?>">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.js"></script>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'].'/../includs/header.php';
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
(`mailusers`.`mail_id` = '$User->id') as `active`,
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
    $q = (object) $q;
	$Profile = new User();
	$Profile->byId($q->mail_id);
    $Profile->iniAva();

	$check = $checks[$q->active];
	?>
	<div class="persrow">
		
		<div class="nicon_out">
			<input type="radio" id="<?php echo $q->mail_id?>" name="muser" value="<?php echo $q->mail_id?>" <?php echo $check?> onchange="this.form.submit()">
			<label class="navicon" for="<?php echo $q->mail_id?>" style="background-image: url(<?php echo $Profile->avatar?>);"></label>
			<div class="persnames">
				<div class="first_name"><?php echo $Profile->fname?></div>
				<div class="last_name"><?php echo $Profile->last_name?></div>
				<div class="mailnick"><?php echo $Profile->mailnick?></div>
			</div>
		</div>
		<?php 
		if($q->active)
		{
			?><a href="exit.php"><button type="button" class="def_button">Выход</button></a><?php
		}
		?>
	</div>
	<span>Публичный ник:<br></span>
	<?php if($q->active)
		{
            ?>

            <input data-tooltip="Публичный ник.<br>Только буквы и цифры<br>3-20 символов." id="public_nick" type="text" name="public_nick" placeholder="Публичный ник" value="<?php echo $Profile->user_nick?>" autocomplete="off" />

            <button type="button" id="sendnick" class="def_button" style="display: none;">Ok</button>
            <span style="color: green; font-size: 20px;" id="Nick_Ok"></span>
            <?php
		}else
		{
			echo '<b>'.$Profile->user_nick.'</b>';
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
include_once 'pageb/footer.php';
if(!$User->ismobiledevice)
    addScript('js/tooltips.js');
addScript('js/profile.js');
?>
</body>
</html>