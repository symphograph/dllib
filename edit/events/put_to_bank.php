<?php
if(!isset ($_POST['bank'])) {echo 'Ничего не вижу'; exit();};
include '../../includs/ip.php'; 
$bank_id = $_POST['bank'];
$bill = $_POST['bill'];
$editor = $_POST['nick'];
include("../../includs/config.php");
qwe("INSERT INTO `ev_banks_log` (`bank_id`,`bill`, `time`, `editor`) VALUES ('$bank_id', '$bill', now(), '$editor')");
qwe("UPDATE `event_banks` SET `bill` = '$bill' where `bank_id` = '$bank_id'");
echo '<meta http-equiv="refresh" content="0; url=event_bills.php">';
?>