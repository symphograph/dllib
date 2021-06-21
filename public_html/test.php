<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die('rrr');

$c = 10/3;
echo $c;
//3.3333333333333
echo $c === 3.3333333333333 ? 'да' : 'нет';
//нет


//echo '<br><br>'. (microtime(true) - $tstart);
?>

