<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';
use Transfer\MailRuUser;

$email = $_POST['email'] ?? '';
if(empty($email)){
    die(http_response_code(300));
}

$User = MailRuUser::byEmail($email)
or die(http_response_code(300));

echo json_encode($User, JSON_UNESCAPED_UNICODE);