<?php

namespace Transfer;

use DTO\PriceDTO;
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
    public ?array $follows = [];
    public ?array $prices = [];

    public static function byEmail(string $email)
    {
        $qwe = qwe("
            select 
                mailusers.*,
                if(us.server, us.server, 9) as server_id
            from mailusers
            left join user_servers us 
                on mailusers.mail_id = us.user_id               
            where email = :email
            and mail_id in (select distinct user_id from prices where server_group > 0)",
        ['email'=>$email]
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchObject(self::class);
    }

    public static function byId(string $id)
    {
        $qwe = qwe("
            select 
                mailusers.*,
                if(us.server, us.server, 9) as server_id
            from mailusers
            left join user_servers us 
                on mailusers.mail_id = us.user_id               
            where email = :email
            and mail_id in (select distinct user_id from prices where server_group > 0)",
            ['id'=>$id]
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        $muser = $qwe->fetchObject(self::class);
        $muser->initData();
        return $muser;
    }

    /**
     * @return array<string>|false
     */
    public static function getEmails(): array|false
    {
        $qwe = qwe("
            select email from mailusers 
            where email like '%@%'
            and mail_id in (select distinct user_id from prices where server_group > 0)"
        );
        if(!$qwe || !$qwe->rowCount()){
            return false;
        }
        return $qwe->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return self[]
     */
    public static function getList(): array|false
    {
        $qwe = qwe("
            select mu.*,
                   if(us.server, us.server, 9) as server_id
            from mailusers mu
            left join user_servers us 
                on mu.mail_id = us.user_id
            where email like '%@%'
            and (
                mail_id in (select distinct user_id from prices where server_group > 0)
                or
                mail_id in (select distinct user_id from folows)
                or 
                mail_id in (select distinct folow_id from folows)
                )"
        );

        /** @var self[] $arr */
        $arr = $qwe->fetchAll(PDO::FETCH_CLASS, self::class);
        $List = [];
        foreach ($arr as $muser){
            $muser->initData();
            $List[] = $muser;
        }
        return $List;
    }

    private function initData(): void
    {
        self::initFollows();
        self::initPrices();
    }

    private function initFollows(): void
    {
        $qwe = qwe("select folow_id from folows where user_id = :user_id", ['user_id'=>$this->mail_id]);
        if(!$qwe || !$qwe->rowCount()){
            return;
        }
        $this->follows = $qwe->fetchAll(PDO::FETCH_COLUMN);
    }

    private function initPrices(): void
    {
        $this->prices = PriceDTO::getListOfUser($this->mail_id);
    }
}