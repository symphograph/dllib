<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$puser = $_POST['puser'] ?? 0;
$puser = intval($puser);
if(!$puser)
    die('puser');

if(empty($_POST['cond'])){
    die('cond');
}

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$Puser = new User();
if(!$Puser->byId($puser)){
    die('no exist');
}

if($_POST['cond'] === 'true'){
    $User->addFolow($puser);
}

if($_POST['cond'] === 'false'){
    $User->delFolow($puser);
}

echo json_encode([]);

