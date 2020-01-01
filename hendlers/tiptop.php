<?php
//var_dump($_POST);
if(empty($_POST['tiptop']))
	die('empty');
$tiptop = intval($_POST['tiptop']);
if($tiptop != 1)
	die('ttt');
include_once '../includs/ip.php';
include_once '../includs/config.php';

$qwe = qwe("
SELECT max(tip_id) as max FROM tiptops  
");
if(!$qwe or $qwe->num_rows == 0)
	die;
$qwe = mysqli_fetch_assoc($qwe);
$max = $qwe['max'];
$rand = random_int(1, $max);

$qwe = qwe("
SELECT * FROM tiptops 
WHERE tip_id  = '$rand'");
if(!$qwe or $qwe->num_rows == 0)
	die('sql');
$qwe = mysqli_fetch_assoc($qwe);
echo $qwe['tip_text'];
?>