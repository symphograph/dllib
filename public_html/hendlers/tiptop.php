<?php
$tiptop = $_POST['tiptop'] ?? 0;
$tiptop = intval($tiptop);
if($tiptop != 1)
	die();
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
//echo 'Я тут кое-что переделываю. Некоторые функции могут быть временно недоступны. Скоро всё заработает.';
//die();
$qwe = qwe("
SELECT tip_id FROM tiptops  
");
if(!$qwe or !$qwe->rowCount())
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
if(!$qwe or !$qwe->rowCount())
	die('sql');

$q = $qwe->fetchObject();
echo $q->tip_text;
?>