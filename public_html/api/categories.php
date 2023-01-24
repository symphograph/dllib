<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/includs/config.php';

use api\{Api, Category};
$List = Category::getTree();
echo Api::resultData($List);