<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

$User = new User();
if(!$User->byIdenty()){
    die('user');
}

$mode = $_POST['mode'] ?? 0;
$server = intval($mode);
if(!$mode)
    die('mode');

if(!$User->setMode($server)){
    die('setError');
}

echo json_encode([]);

