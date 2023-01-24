<?php

namespace Transfer;

use PDO;

class MailRuUser
{
    public ?int    $mail_id;
    public ?string $first_name;
    public ?string $last_name;
    public ?int    $age;
    public ?string $email;
    public ?string $time;
    public ?string $last_time;
    public ?string $avatar;
    public ?string $mailnick;
    public ?string $ip;
    public ?string $last_ip;
    public ?string $identy;
    public ?string $token;
    public bool    $siol = false;
    public ?string $user_nick;
    public ?string $avafile;
    public ?int    $mode;
    public ?int    $server_id;

    public static function byEmail(string $email)
    {
        $qwe = qwe("
            select 
                mailusers.*,
                if(us.server, us.server, 9) as server_id
            from mailusers
            left join user_servers us 
                on mailusers.mail_id = us.user_id               
            where email = :email",
        ['email'=>$email]
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchObject(self::class);
    }

    /**
     * @return array<string>|false
     */
    public static function getEmails(): array|false
    {
        $qwe = qwe("
            select email from mailusers 
            where email like '%@%'
            and mail_id in (select distinct user_id from prices)"
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_COLUMN);
    }
}