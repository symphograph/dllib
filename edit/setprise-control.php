<?php
$item_id=trim($_POST['item_id']);
$setname=trim($_POST['setname']);
session_start();
$_SESSION['query'] = $setname;
ini_set('display_errors',1);
error_reporting(E_ALL);
include "../includs/config.php";
if(isset($_POST['cancel']))
{echo '<center>Тогда в другой раз.</center>';
echo '<meta http-equiv="refresh" content="2; url=../catalog.php">'; 
  exit();};

if(!empty($_POST['item_id'])){
	include "../includs/config.php"; // Подключение к БД.
		
$setgold=$_POST['setgold'];
$setsilver = $_POST['setsilver'];
$setbronze = $_POST['setbronze'];
$user = $_COOKIE['cluid'];
$nick = $_COOKIE['nick'];
//$setgold=2;
//$setsilver = 94;
//$setbronze = 56;
$setprise = $setgold*10000+$setsilver*100+$setbronze;
$query = mysqli_escape_string($setname);

		 qwe("UPDATE items SET  price = '$setprise' where item_id = '$item_id'");
		 qwe("UPDATE items SET  looked = 1 where item_id = '$item_id'");
qwe("INSERT INTO updates (item_name, user, time, price, item_id, edit_type, nick) VALUES ('$setname', '$user', now(), '$setprise', '$item_id', 'setprice', '$nick')");	
	echo '<center>Записано: '.$query.' id='.$item_id.' '.$setprise.'</center>';
	echo '<meta http-equiv="refresh" content="2; url=../catalog.php">';	
		};
	exit();

	?>
