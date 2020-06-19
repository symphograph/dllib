<?php
if(!isset($_POST['from_id'])) die();
$from_id = intval($_POST['from_id']);
if(!$from_id) die('Откуда?');
$to_id = $_POST['to_id'] ?? 0;
$to_id = intval($to_id);
if(!$to_id) die('Куда?');

include_once $_SERVER['DOCUMENT_ROOT'].'/includs/usercheck.php';
if(!$user_id) die('user_id');

$ptoken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 0;
$ptoken = OnlyText($ptoken);
$token = AskToken();
if((!$token) or (!$ptoken) or $ptoken != $token)
    die('token');


if(!isset($_POST['transport'])) die('На чём?');
$transport = intval($_POST['transport']);
//var_dump($transport);
if (!$transport) die('На чём?');

$time = intval($_POST['time']);
if(!$time) die('Время?');
if($time < 0) die('Время?');
if($time > 60) die('Это слишком долго');


$buff_1 = $buff_2 = $buff_3 = null;
if(isset($_POST['buff']))
{
    $buff_1 = $_POST['buff'][1] ?? null;
    $buff_2 = $_POST['buff'][2] ?? null;
    $buff_3 = $_POST['buff'][3] ?? null;

    $buff_1 = intval($buff_1);
    $buff_2 = intval($buff_2);
    $buff_3 = intval($buff_3);
}
if($transport != 1)
    $buff_1 = 0;

$qwe = qwe("
Select * from user_routimes 
where (user_id, from_id, to_id, transport, buff_1, buff_2, buff_3) 
          = 
      ($user_id, $from_id, $to_id, $transport, $buff_1, $buff_2, $buff_3)");
if($qwe and $qwe->num_rows > 0)
{
    $qwe = mysqli_fetch_assoc($qwe);
    $dur_id = $qwe['dur_id'];
}else
{
    $dur_id = EmptyIdFinder('user_routimes','dur_id');
}

qwe("
replace into user_routimes
(dur_id,user_id, from_id, to_id, transport, buff_1, buff_2, buff_3,durway,time) VALUES 
('$dur_id', '$user_id', '$from_id', '$to_id', '$transport', '$buff_1', '$buff_2', '$buff_3', '$time', now())
");

?>