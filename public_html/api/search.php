<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use api\{Api, item\Item};

$List = Item::searchList();
echo Api::resultData($List);