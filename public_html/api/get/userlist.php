<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use Transfer\MailRuUser;

$List = MailRuUser::getEmails()
or die(http_response_code(300));

echo json_encode($List, JSON_UNESCAPED_UNICODE);