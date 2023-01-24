<?php

use api\Api;
use api\item\Item;

require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
$id = intval($_POST['id'] ?? 0) or
die(Api::errorMsg('id'));

$Item = Item::byId($id) or die(Api::errorMsg());
$Item->initInfo();
$Item->Info->initCategory($Item->categ_id);
echo Api::resultData($Item);
