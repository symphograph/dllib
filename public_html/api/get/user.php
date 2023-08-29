<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use CTRL\MailRuUserCTRL;
use Transfer\MailRuUser;

match ($_POST['method']) {
    'getById' => MailRuUserCTRL::getById(),
    'getByEmail' => MailRuUserCTRL::getByEmail(),
    'list' => MailRuUserCTRL::list(),
    default => http_response_code(400)
};

$mail_id = $_POST['mail_id']
    ?? die(http_response_code(400));

$User = MailRuUser::byId($mail_id)
or die(http_response_code(500));

echo json_encode($User, JSON_UNESCAPED_UNICODE);