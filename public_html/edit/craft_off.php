﻿<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) exit();
if(!empty($_POST['craft_off']))
{
	$craftid = intval($_POST['craftid']);
	require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
	qwe("UPDATE `crafts` SET `on_off` = '0' WHERE `craft_id` = '$craftid'");
	echo '<center>Рецепт '.$craftid.' отключен</center>';
}
else
{echo '<center>Что-то не так</center>';};
echo '<meta http-equiv="refresh" content="; ../catalog.php">';
?>
