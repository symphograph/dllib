<meta charset="utf-8">
<?php
$tstart = microtime(true);

if(!isset($cfg)) {
    $cfg = require dirname($_SERVER['DOCUMENT_ROOT']).'/includs/ip.php';
    require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
}
if (!$cfg->myip)
    die('rrr');
$User = new User;
$User->check();
$Craft = new Craft(48);
$Craft->InitForUser();
printr($Craft);


echo '<br><br>'. (microtime(true) - $tstart);
?>

