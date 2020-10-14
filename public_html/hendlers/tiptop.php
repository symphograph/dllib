<?php
//var_dump($_POST);
if(empty($_POST['tiptop']))
	die('empty');
$tiptop = intval($_POST['tiptop']);
if($tiptop != 1)
	die('ttt');
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/config.php';

$qwe = qwe("
SELECT tip_id FROM tiptops  
");
if(!$qwe or $qwe->num_rows == 0)
	die;
foreach ($qwe as $q)
{
    $arr[] = $q['tip_id'];
}
$rand = array_rand($arr);
$rand = $arr[$rand];


//$rand = random_int(1, $max);

$qwe = qwe("
SELECT * FROM tiptops 
WHERE tip_id  = '$rand'");
if(!$qwe or $qwe->num_rows == 0)
	die('sql');
$qwe = mysqli_fetch_assoc($qwe);
echo $qwe['tip_text'];
?>