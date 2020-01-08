<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php';
include('../../tscheck.php');
if($group_lvl < 1){echo '<meta charset="utf-8">Нет доступа.'; exit();}
if($group_lvl < 5 and !$myip){echo '<meta charset="utf-8">Привет, '.$nick.'!
<br>Рад тебя видеть, но тебе сюда не надо.:)<br>'; exit();}
?>
<html>
<head>
 <meta charset="utf-8"><meta name="robots" content="noindex, nofollow"/>
  <title> Состояние банков </title>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
 
<body>
 
 <form method="POST" action="put_to_bank.php">
   <select name="bank" id="myForm" autocomplete="off" onchange="getAjax(); return false;">
<?php 
	   include("../../includs/config.php");
	   echo '<option value="0" checked >Выбрать банк</option>';
    $ev_query = qwe("SELECT * FROM `event_banks`");
	//print_r($events);
	foreach($ev_query as $v){
		echo '<option value="'.$v['bank_id'].'">'.$v['bank_name'].'</option>';
	}
	   ?>
    </select>
    <input type="hidden" name="nick" value="<?php echo $nick;?>"> 
<script type="text/javascript">
//<![CDATA[
function getAjax(){
$.ajax({
 url: "bill_action.php", // путь к ajax файлу
 type: "POST",      // тип запроса
 data: { // действия
   act: $('#myForm').val()
 },
 // Данные пришли
 success: function(data ) {
   $("#view" ).html("<strong>" + data + "</strong>" );
  }
});
 
}
 
//]]>
</script>
<div id="view"></div>
 </form>
</body>
</html>