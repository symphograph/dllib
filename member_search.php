<?php
require_once 'includs/ip.php'; 
include("tscheck.php");
echo '<meta charset="utf-8">';
if($group_lvl < 4){echo 'Нет доступа.'; exit();}
//if(isset($_POST['leader']))
	$rl_niput = 'member'; //else $rl_niput = 'leader'; 
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Редактор рейда</title>
<script src="//yandex.st/jquery/1.7.2/jquery.min.js"></script>
 <script type="text/javascript" src="js/member_search.js"></script>
 <link href="css/member_search.css?ver=1" rel="stylesheet">
</head>

<body>
<?php
if(isset($_GET['event_id']))
{
	$event_id = $_GET['event_id'];
}
?>
<form action="" method="post">
  <?php echo
   '<p><input name="'.$rl_niput.'" class="raid_cell" list="members" title="Жми Enter, чтобы отправить"></p>
   <datalist  id="members">';

	  // if(!isset($_POST['leader']))
		  $query = qwe("
		  SELECT `cldbid`, `sh_nick` 
		  FROM `ts_us_groups` 
		  WHERE `group_id` 
          in (SELECT `group_id` 
		      FROM `ts_groups` 
			  WHERE group_lvl > 3) 
			  GROUP BY `cldbid`");
	  // else
	$query = qwe("SELECT `cldbid`, `sh_nick` FROM `ts_users` GROUP BY `sh_nick`");  
	 foreach($query as $v)
	 {
		 $member_id = $v['cldbid'];
		 $sh_nick = $v['sh_nick'];
		 echo '<option  value="'.$member_id.'">'.$sh_nick.'</option>';
	 }
	   
?>
    
   </datalist>
  </form> 
</body>
</html>