<?php
$ip = $_SERVER['REMOTE_ADDR'];
if($ip == '37.194.65.246') 
{
	//echo 'Нет доступа'; exit();
ini_set('display_errors',1);
error_reporting(E_ALL);};
if(isset($_POST['member']))
{echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta charset="utf-8">
В рейде были:<br>'; 
					
			$member = $_POST['member']; $i=0;
			//print_r($member);
 $event = 'test';
 $leader = 'test';
			/*foreach($member as $key){
				echo $key.'<br>';};*/
			$members = implode(', ',$member);
include('includs/config.php');
qwe("INSERT INTO `events` (`event`, `leader`, `members`, `time`) 
VALUES ('$event', '$leader', '$members', now())");
 $query = qwe("SELECT * FROM `ts_us_groups` WHERE `cldbid` in ($members)");
 $evid_q = qwe("SELECT max(id) from `events`");
 $evidarr = mysqli_fetch_row($evid_q);
$evid = $evidarr[0]; 
 foreach($query as $key){
	$gid = $key['cldbid'];
	$gnick = $key['sh_nick'];
 echo $gnick.'<br>';
 qwe("INSERT INTO `ev_members` (`event_id`, `member_id`) 
VALUES ($evid, $gid)");}
 ?>
 <b>Записал.</b><br>
 <a href="lut-t1.php"><button style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">Назад</button></a>
 
 <?php
 //echo '<meta http-equiv="refresh" content="5; url=lut-t1.php">';
exit();}
/////////////
include('includs/config.php');
$query = qwe("SELECT * FROM `ts_us_groups` GROUP BY `sh_nick`");

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="robots" content="noindex, nofollow"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Документ без названия</title>
</head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
<body style="width: 200px">
<form action="" method="post">
<header style="position: fixed; margin-left: 40%;">
Насчитал: <span id="count"></span><br>
<button style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">Сбросить</button><br><br>
<input type="submit" value="Записать" style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">
</header><br>


<div style="margin-top: 30px">
<?php 
	$rest = 'A'; $i = 0;
	foreach($query as $key){
	$gid = $key['cldbid'];
	$gnick = $key['sh_nick'];
	/*$rest2 = mb_substr($gnick, 0, 1);
		if($rest2 !== $rest)echo $rest2.'<hr>';
		$rest = $rest2;*/
	echo 
'<p><input name="member[]" id="'.$gid.'" type="checkbox" value="'.$gid.'"> '.$gnick.'</p>'; $i++;}
?>
</div>
<div style="position: fixed; margin-left: 70%; margin-top: 70%;"></div>
</form>
<script type="text/javascript">

    var count = 0;

    $(function() {
        displayCount();
        $('input[type=checkbox]').click(function() {
            if (this.checked) {
                count++;
            } else {
                count--;
            }
            displayCount();
        });
        /*$('#invert').click(function(e) {
            e.preventDefault();
            $('input[type=checkbox]').click();
        });*/
    });

    function displayCount() {
        $('#count').text(count);
    }

</script>

<script>
$('button').click(function() {
  $('input:checked').prop('checked', false);
});
</script>
</body>
</html>