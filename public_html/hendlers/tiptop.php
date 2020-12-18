<?php
$tiptop = $_POST['tiptop'] ?? 0;
$tiptop = intval($tiptop);
if($tiptop != 1)
	die();
if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
//echo 'Я тут кое-что переделываю. Некоторые функции могут быть временно недоступны. Скоро всё заработает.';
//die();
$qwe = qwe("
SELECT tip_id FROM tiptops  
");
if(!$qwe or !$qwe->num_rows)
	die;

foreach ($qwe as $q)
{
    $arr[] = $q['tip_id'];
}
$rand = array_rand($arr);
$rand = $arr[$rand];


$qwe = qwe("
SELECT * FROM tiptops 
WHERE tip_id  = '$rand'");
if(!$qwe or !$qwe->num_rows)
	die('sql');
$qwe = mysqli_fetch_assoc($qwe);
echo $qwe['tip_text'];
?>