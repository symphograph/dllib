<meta charset="utf-8">
<?php
include 'includs/ip.php';
include 'functions/functions.php';
//header('Content-type: text/plain');
$now = date('d.m.y H:i');
echo '<p>'.$now.'</p>';
$time = date('H:i');
if(!empty($_POST['time']))
$time = $_POST['time'];
var_dump($_POST['time']);

?>
<br>
<form action="" method="post">
<input name="time" type="time" value="<?php echo $time;?>"/>
<input type="submit" value="Ok"/>
</form>


<?php
$event_time = 24;
$server_offset = 3*60*60;
$time_arr = explode(':',$time);
$hour = $time_arr[0];
$min = $time_arr[1];
$mintime = $hour*60+$min;//игровых минут с полуночи
$game_next = $event_time*60-$mintime;//через сколько игровых минут событие
$midnight_time = time() - ceil($mintime/6)*60; //Во сколько была игровая полночь
echo '<p>Игровая полночь была в: '.date('H:i',$midnight_time).'</p>';
echo '<p>'.$game_next.'</p>';
echo date('H:i',$game_next*60-$server_offset);
$real_next = ceil($game_next/6);
echo '<p>До ближайшей призрачки '.$real_next.' реальных минут</p>';
echo '<p>До ближайшей призрачки осталось '.date('H:i',$real_next*60-$server_offset).' реального времени</p>';
$next = time()+($real_next*60);
$dnext = date('H:i d.m.Y' , $next);
echo '<p>Время ближайшей призрачки: '.$dnext.' МСК</p>';
echo '<p>Время ближайшей призрачки на Сахалине: '.date('H:i d.m.Y' , time()+($real_next*60+8*60*60) ).'</p>';

//echo $mintime;
?>
<p>Расписание:</p>
<?php 
$date = date('d.m' , $next);
for($i=1;$i<43;$i++)
{
$dnext = date('H:i' , $next);
?><p> <?php echo $i.'. '.$dnext ?></p><?php
$next = $next+60*60*4;
if($date != date('d.m' , $next))
	echo $date;
$date = date('d.m' , $next);
}
?>