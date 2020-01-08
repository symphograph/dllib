<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php'; 
include("../../tscheck.php");
echo '<meta charset="utf-8">';
if($group_lvl < 1){echo 'Нет доступа.'; exit();}
if($group_lvl < 4){echo 'Привет, '.$nick.'!
<br>Рад тебя видеть, но тебе сюда не надо.:)<br>'; exit();}

if(!empty($_POST['addmemb'])) 
{
	include("addmemb.php");};
if(isset($_POST['member']))
{if($_POST['event'] == 0) {echo 'И в какой рейд это всё записывать?'; exit();}
 $date = $_POST['date'];
	include("member.php"); exit();}
/////////////
$query = qwe("SELECT max(`lastconnected`) as `last`, `cldbid`, `sh_nick` FROM `ts_users` GROUP BY `sh_nick`");
//$query_add = qwe("SELECT min(`id`) as `id`, `sh_nick` FROM `ev_membs_add` GROUP BY `sh_nick`");
$img_gold = '<img src="../../img/gold.png" width="15" height="15" alt="gold"/>';
$dare_ph = date("j.n.Y");
$rl_q = qwe("SELECT `cldbid`, `sh_nick` FROM `ts_us_groups` WHERE `group_id` 
in (SELECT `group_id` FROM `ts_groups` WHERE group_lvl > 3) GROUP BY `cldbid`");
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="robots" content="noindex, nofollow"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Отмечалка</title>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
<body style="width: 200px">
<form action="" method="post">
<header style="position: fixed; margin-left: 40%;">

Привет, <?php echo $nick;?>!<br>
Насчитал: <span id="count"></span><br>
<button style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px">Сбросить</button><br><br>
<input type="submit" value="Записать" style="width: 200px; height: 50px; border-radius: 10px; margin-top: 20px"><br><br>

	<textarea name="addmemb" style="width: 200px" rows="3" autocomplete="off" placeholder="Если кого-то нет в списке, пиши сюда ники. Разделяй пробелами."></textarea>
</header><br>


<div style="margin-top: 30px">
<input name="date" type="date" required placeholder="<?php echo $dare_ph;?>">
<p><select name="leader" autocomplete="off" required>
<option value="">РЛ</option>
<?php
foreach($rl_q as $v){
		echo '<option value="'.$v['cldbid'].'">'.$v['sh_nick'].'</option>';
	}	
?>	
</select></p><br>
<p><select name="event" autocomplete="off" required>
<?php
	
	$ev_query = qwe("SELECT * FROM `event_types` order by `ev_categ`, `event_name`");
	echo '<option value="">Мероприятие</option>';
	foreach($ev_query as $v){
		echo '<option value="'.$v['event_t_id'].'">'.$v['event_name'].'</option>';
	}
		
		?>
</select></p><br>
<?php 
	$rest = 'A';
	$latin = true; $det = '</details>';
	echo '<br><details><summary>Латиница</summary>';
	echo '<br><details><summary>'.$rest.'</summary>';
	foreach($query as $key){
	$gid = $key['cldbid'];
	$gnick = $key['sh_nick'];
	if(ctype_digit($gnick) or $gnick == '') continue;
	$rest2 = mb_substr($gnick, 0, 1);
		if (preg_match("/[а-я]+/i", substr($gnick, 0, 1)) and $latin)
		{	$latin = false; $det = '';
		echo '</details></details><hr><details><summary>Кириллица</summary>';
		}
		if($rest2 !== $rest)echo $rest2.$det.'<hr><details><summary>'.$rest2.'</summary>';
		$rest = $rest2; $det = '</details>';
	echo 
'<p><input name="member[]" id="'.$gid.'" type="checkbox" value="'.$gid.'"> '.$gnick.'</p>';}
	echo '</details></details>';
	/*
	echo
	'<hr><details><summary>Вне ТС</summary>';
	foreach($query_add as $key){
	$gid = $key['id'];
	$gnick = $key['sh_nick'];
	$gnick = substr($gnick, 0, -6);
	echo 
'<p><input name="member[]" id="'.$gid.'" type="checkbox" value="'.$gid.'"> '.$gnick.'</p>';}
	echo '</details>';
	*/
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