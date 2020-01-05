 <?php
 
 $event = $_POST['event'];
$ev_name_q = qwe("SELECT `event_t_id`, `event_name` FROM `event_types` WHERE `event_t_id` = '$event'");
foreach($ev_name_q as $n)
{
$event_name = $n['event_name'];	
}
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta charset="utf-8">
В рейде "'.$event_name.'" были:<br>'; 
					
			$member = $_POST['member']; $i=0;
//сливаем с новенькими
 if(isset($addgids))
 {
	 if(count($addgids)>0)
	 {$member = array_merge($member,$addgids);};
 }

 
			
			$members = implode(', ',$member);
$leader =  $_POST['leader'];
//записываем рейд
qwe("INSERT INTO `events` (`event_t_id`, `editor`, `leader`, `time`, `date`) 
VALUES ('$event', '$cldbid', '$leader', now(), '$date')");

 $query = qwe("SELECT * FROM `ts_users` WHERE `cldbid` in ($members)");
 $evid_q = qwe("SELECT max(id) from `events`");
 $evidarr = mysqli_fetch_row($evid_q);
$evid = $evidarr[0]; 
 foreach($query as $key){
	$gid = $key['cldbid'];
	$gnick = $key['sh_nick'];
 echo $gnick.'<br>';
 qwe("INSERT INTO `ev_members` (`event_id`, `member_id`) 
VALUES ($evid, $gid)");}

//Лишний
/*
if(isset($adds))
{
 $query = qwe("SELECT * FROM `ev_membs_add` WHERE `sh_nick` in ($adds)");
 foreach($query as $key){
	$gid = $key['id'];
	$gnick = $key['sh_nick'];
 echo $gnick.'<br>';
 qwe("INSERT INTO `ev_members` (`event_id`, `member_id`) 
VALUES ($evid, $gid)");}
}

$query = qwe("SELECT * FROM `ev_membs_add` WHERE `id` in ($members)");
foreach($query as $key){
	$gid = $key['id'];
	$gnick = $key['sh_nick'];
 echo $gnick.'<br>';
 qwe("INSERT INTO `ev_members` (`event_id`, `member_id`) 
VALUES ($evid, $gid)");}
*/
 echo 
 '<b>Записал.</b><br>
 <a href="eventadm.php"><button style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">Назад</button></a>
 <a href="../../eventlist.php?recount=1"><button style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">В смотрелку</button></a>';
?>