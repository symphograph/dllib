<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use Transfer\MailRuUser;

$List = MailRuUser::getList()
or die(http_response_code(500));

echo json_encode($List);