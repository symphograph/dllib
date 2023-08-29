<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
use Transfer\MailRuUser;

$mail_id = $_POST['mail_id'] ?? '';
if(empty($email)){
    die(http_response_code(300));
}

$User = MailRuUser::byId($mail_id)
or die(http_response_code(300));

echo json_encode($User, JSON_UNESCAPED_UNICODE);