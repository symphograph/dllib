<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    die();

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$User = new User();
if(!$User->byIdenty()){
    die('user');
}
if(empty($_POST['prices'])){
    die('no data');
}

if(!is_array($_POST['prices'])){
    die('no arr');
}
$ok = true;
foreach ($_POST['prices'] as $p){
    $pp = array_map('intval',$p);
    $Price = new Price($pp['item_id']);
    if(!$Price->insert($pp['price'])){
        $ok = false;
    }
}

echo json_encode([]);

