<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$server = $_POST['server'] ?? 0;
$server = intval($server);
if(!$server)
    die('server');

if(!$User->setServer($server)){
    die('setError');
}

echo json_encode([]);

