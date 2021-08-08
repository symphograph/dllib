<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$puser = $_POST['puser'] ?? 0;
$puser = intval($puser);
if(!$puser)
    die('puser');

$Puser = new User();
$Puser->byId($puser);

$info = $Puser->getPublicInfo();
$info['isFolow'] = $User->isFolow($puser);
echo json_encode($info);

