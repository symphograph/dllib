<?
include '../../includs/ip.php'; 
include("../../tscheck.php");
echo
'<meta charset="utf-8">';
if($group_lvl < 4){echo 'Нет доступа.'; exit();}

if(isset($_GET['event_id']) and ctype_digit($_GET['event_id']))	
{
	$event_id = $_GET['event_id'];
echo 
'<form name="confirm" action="" method="post">
	Ты собираешься удалить запись о рейде.<br>
	Это действие нельзя отменить.
	<input type="hidden" name="event_id" value="'.$event_id.'">
	<br><input type="submit" value="Да. Удаляй">
	<a href="../../eventlist.php">Я передумал</a>
</form>';
}

if(isset($_POST['event_id']) and ctype_digit($_POST['event_id']))
{   $event_id = $_POST['event_id'];
	qwe("
	DELETE FROM `events` 
	WHERE `id` = '$event_id'");
  echo '<meta http-equiv="refresh" content="0; url=../../eventlist.php?recount=1">';
	
}
?>

