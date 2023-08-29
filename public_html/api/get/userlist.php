<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use Transfer\MailRuUser;

$List = MailRuUser::getIds()
or die(http_response_code(500));

echo json_encode($List, JSON_UNESCAPED_UNICODE);