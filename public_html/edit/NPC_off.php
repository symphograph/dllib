<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip !== '37.194.65.246'){
echo '<center>Что-то не так</center><meta http-equiv="refresh" content="2; ../catalog.php">';
exit();};
if(isset($_POST['NPC_off']))
{$item_id = $_POST['item_id'];
$user = $_POST['nick'];

qwe("UPDATE `items` SET `is_trade_npc` = '0' WHERE `item_id` = '$item_id'");
qwe("INSERT INTO updates (item_id, user, time, edit_type) VALUES ('$item_id', '$user', now(), 'NPC_off')");
echo '<center>Предмет '.$_POST['item_id'].' теперь нельзя купить у NPC</center>';}
else
{echo '<center>Что-то не так</center>';};
echo '<meta http-equiv="refresh" content="2; ../catalog.php">';
?>