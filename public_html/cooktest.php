<meta charset="utf-8">
<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/ip.php';
if(!$myip) exit();
require_once $_SERVER['DOCUMENT_ROOT'].'/../includs/usercheck.php';


printr($_GET);
printr($_COOKIE);


?>

