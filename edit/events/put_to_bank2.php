<?php
if(!isset ($_POST['bill'])) {echo 'Ничего не вижу'; exit();};
require_once $_SERVER['DOCUMENT_ROOT'].'/includs/ip.php'; 
//$bank_id = $_POST['bank'];
$bill = $_POST['bill'];
$editor = $_POST['nick'];
$start = $_POST['start'];
$end = $_POST['end'];
include("../../includs/config.php");
foreach($bill as $k => $v)
{
qwe("INSERT INTO `ev_banks_log` (`bank_id`,`bill`, `time`, `editor`) VALUES ('$k', '$v', now(), '$editor')");
qwe("UPDATE `event_banks` SET `bill` = '$v' where `bank_id` = '$k'");
}
qwe("UPDATE `ev_start` SET `start_date` = '$start', `end_date` = '$end'");
echo '<meta http-equiv="refresh" content="0; url=../../eventlist.php?recount=1">';
?>