<?
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php'; 
include("../../tscheck.php");
if($group_lvl < 4 or !isset($_GET['event_id'])){echo '<meta charset="utf-8">Нет доступа.'; exit();}
$event_id = $_GET['event_id'];
$member_id = $_GET['member_id'];
$uri_from = $_SERVER['HTTP_REFERER'];
if(ctype_digit($event_id) and ctype_digit($member_id))
	qwe("
	DELETE FROM `ev_members` WHERE `event_id` = '$event_id' and `member_id` = '$member_id'");

echo '<meta http-equiv="refresh" content="0; url='.$uri_from.'">';
exit();  
?>