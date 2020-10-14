<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) exit();
//var_dump($_GET);
if(!empty($_GET['item_id']))
{$item_id = intval($_GET['item_id']);
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';
qwe("UPDATE `items` SET `on_off` = '0' WHERE `item_id` = '$item_id'");

echo '<center>Предмет '.$item_id.' отключен</center>';}
else
{echo '<center>Что-то не так</center>';};
echo '<meta http-equiv="refresh" content="0; url=../catalog.php">';
?>