<?php
require_once dirname($_SERVER['DOCUMENT_ROOT']).'/includs/config.php';

use CTRL\MailRuUserCTRL;

match ($_POST['method']) {
    'getById' => MailRuUserCTRL::getById(),
    'getByEmail' => MailRuUserCTRL::getByEmail(),
    'list' => MailRuUserCTRL::list(),
    default => http_response_code(400)
};
