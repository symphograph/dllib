<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php'; 
include("../../tscheck.php");
echo '<meta charset="utf-8">';
if($group_lvl < 4 or !isset($_GET['event_id'])){echo 'Нет доступа.'; exit();}
$event_id = $_GET['event_id'];
$admin = true;
//$event_id = $_GET['event_id'];
$rlimg = '<img src="../../img/rl.png" width="11" height="11" alt="РЛ:"/>';
$delimg_size = 'width="24" height="32"';
$delimg = '<img src="../../img/del.png" '.$delimg_size.' title="Удалить рейд"/>';
$delimgsmall = '<img src="../../img/delx.png" width="8" height="8" title="Удалить его из рейда"/>';
$delev = '<div><a href="edit/events/evdel.php?event_id='.$event_id.'">'.$delimg.'</a></div>';

//Запрашиваем о рейде
$query = qwe("
SELECT `events`.`date`, `events`.`id`, `event_types`.`event_t_id`, 
`ts_users`.`sh_nick`, `event_types`.`event_name` 
FROM `events`, `ts_users`, `event_types`
WHERE `ts_users`.`cldbid` = `events`.`leader`
AND `events`.`id` = '$event_id'
AND `events`.`event_t_id` = `event_types`.`event_t_id`");
   foreach($query as $v)
   {
	   $ev_date = date('d.m.y',(strtotime($v['date'])));	
		 $event_name = $v['event_name'];
		 $rl = $v['sh_nick'];
   }
	$rl_niput = 'member'; //else $rl_niput = 'leader'; 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Редактор рейда</title>
<script src="//yandex.st/jquery/1.7.2/jquery.min.js"></script>
 <script type="text/javascript" src="../../js/member_search.js"></script>
 <link href="../../css/ev_editor.css?ver=23" rel="stylesheet">
 <link href="../../css/style.css?ver=9" rel="stylesheet">
</head>
<body>
<?php
include '../../pageb/header.html';

?>
<div class="topw"></div>
<div class="input1"><div class="input2"><div class="inpur">
<?php
 echo '<tr><td class="raid_tr" 
		 ><div class="date"><p>'.$ev_date.'</p></div><div class="event_name"><p>'.$event_name.'</p></div><p><b>'.$rlimg.' '.$rl.'</b>'
		 .$delev.'</p></td>
		 <td id="members">';
			$memb_q = qwe("
			SELECT `cldbid`, `sh_nick` FROM `ts_users` 
            WHERE `cldbid` in 
            (SELECT `member_id` FROM `ev_members` WHERE `event_id` = '$event_id')
			ORDER BY `sh_nick`");
			echo '<div class ="raid">';
			$i=1;
				foreach($memb_q as $n)
			{	
			
			   $member_id = $n['cldbid'];
			   $member = $n['sh_nick'];
				$delmemb = '<div class="del_cell"><a href="delmemb_from_raid.php?event_id='.$event_id.'&member_id='.$member_id.'">'.$delimgsmall.'</a></div>';
				if(preg_match('/_no_ts/',$member))
		 $member = str_replace('_no_ts','',$member);
				if($i == 1)
				{echo '<div class="raid_col">';}
			echo '<div class="raid_cell" title="'.$member_id.'"><div class="nick">'.$member.' '.$delmemb.'</div></div>';
			   if($i == 5)
				{echo '</div>';$i=1; continue;}
				$i++;
			}
	/*	 
	if($i == 1)
				{echo '<div class="raid_col">';}
			echo '<div class="raid_cell"><div class="nick">'.$member.'</div></div>';
			   if($i == 5)
				{echo '</div>';}
	*/
		 	

?>



<!--Последняя ячейка-->
<form action="add_to_raid.php" method="post">
 
  <?php echo
   '<input type="hidden" value="'.$event_id.'" name="event_id">
   <input type="text" name="member_id" class="raid_cell_add" list="members" title="Жми Enter, чтобы отправить" autocomplete="off">
   <datalist  id="members">';
	$query = qwe("SELECT `cldbid`, `sh_nick` FROM `ts_users` GROUP BY `sh_nick`");  
	 foreach($query as $v)
	 {
		 $member_id = $v['cldbid'];
		 $sh_nick = $v['sh_nick'];
		 echo '<option  value="'.$member_id.'">'.$sh_nick.'</option>';
	 }
	 echo '</datalist>';
	//echo '</div></td></tr>';
		?>
  </form>
  </div></td></tr>
  </div></div></div>
</body>
</html>