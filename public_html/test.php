<meta charset="utf-8">
<?php
$tstart = microtime(true);

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
if (!$cfg->myip)
    die('rrr');


    for($a = 0; $a != 1; $a += 0.1){
        echo $a;
    }

//$a станет = 1 никогда


die();


$c = 10/3;
echo $c;
//3.3333333333333
echo $c === 3.3333333333333 ? 'да' : 'нет';
//нет
echo $c*3;
$b = 3;
echo $c*$b;
$d = $c;
echo $d*3;


//echo '<br><br>'. (microtime(true) - $tstart);
?>

