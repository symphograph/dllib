<?
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php'; 
include("../../tscheck.php");
if($group_lvl < 4 or !isset($_POST['event_id'])){echo '<meta charset="utf-8">Нет доступа.'; exit();}
$event_id = $_POST['event_id'];
$member_id = $_POST['member_id'];
	if(ctype_digit($member_id))
	{	
		qwe("
		INSERT INTO `ev_members` (`event_id`, `member_id`) 
		VALUES ('$event_id', '$member_id')");
	}
else
{
	$mem = mysqli_real_escape_string($dbLink,trim($_POST['member_id'])).'_no_ts';
	include("../../functions/mb_ucfirst2.php");
	 $mem = mb_ucfirst($mem);
	echo $mem;
     qwe("
		INSERT INTO `ev_membs_add` (`sh_nick`, `time`) 
		VALUES ('$mem', now())");
	 $query = qwe("
	 SELECT * FROM `ev_membs_add` ORDER BY `id` DESC LIMIT 1");
	foreach($query as $v)
	{
		$member_id = $v['id'];
		$sh_nick = $v['sh_nick'];
	}
    qwe("
		REPLACE INTO `ts_users` (`cldbid`, `sh_nick`) 
		VALUES ('$member_id', '$sh_nick')");
	qwe("
		INSERT INTO `ev_members` (`event_id`, `member_id`) 
		VALUES ('$event_id', '$member_id')");
}
echo '<meta http-equiv="refresh" content="0; url=ev_editor.php?event_id='.$event_id.'">';
exit(); 
?>