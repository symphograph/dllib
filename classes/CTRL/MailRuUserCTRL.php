<?php

namespace CTRL;

use Api\Api;
use Transfer\MailRuUser;

class MailRuUserCTRL extends MailRuUser
{


    public static function getById(): void
    {
        $mail_id = $_POST['mail_id']
            ?? die(http_response_code(400));
        $MailUser = self::byId($mail_id)
            or die(http_response_code(500));
        echo json_encode($MailUser,JSON_UNESCAPED_UNICODE);
        die();
    }

    public static function getByEmail(): void
    {
        $email = $_POST['email']
            ?? die(http_response_code(400));
        $MailUser = self::byEmail($email)
            or die(http_response_code(500));
        echo json_encode($MailUser,JSON_UNESCAPED_UNICODE);
        die();
    }

    public static function list(): void
    {
        $List = self::getList()
            or die(http_response_code(500));
        echo json_encode($List,JSON_UNESCAPED_UNICODE);
        die();
    }


}