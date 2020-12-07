<?php


class User
{
    public $age;
    public $ismobiledevice;
    public bool $uncustomed;
    public int $id = 0;
    public int $mode;
    public int $orcost = 250;
    public int $server = 9;
    public int $server_group = 2;
    public int $siol = 0;
    public string $avafile;
    public string $avatar = 'img/8001096.png';
    public string $email;
    public string $first_ip;
    public string $first_name;
    public string $identy;
    public string $last_ip;
    public string $last_name;
    public string $last_time;
    public string $mailnick;
    public string $time;
    public string $token;
    public string $user_nick;
    public array $profs = [];
    public $agent;

    public function ById(int $user_id)
    {
        $qwe = qwe("
		SELECT `mailusers`.*,
		`user_servers`.`server`,
		`servers`.`server_group`,
		`servers`.`server_name`
		FROM
		`mailusers`
		LEFT JOIN `user_servers` ON `user_servers`.`user_id` = `mailusers`.`mail_id`
		LEFT JOIN `servers` ON `servers`.`id` = `server`
		WHERE BINARY `mailusers`.`mail_id` = '$user_id'
		");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        self::ByQwe($q);

        return true;
    }

    public function ByGlobal()
    {
        $arr = UserInfo();
        if (!$arr)
            return false;
        $arr = (object) $arr;
        $user_id = $arr->user_id;
        $this->id = $arr->user_id;
        $this->identy = $arr->identy;
        $this->server_group = $arr->server_group;
        $this->server = $arr->server;
        $this->fname = $arr->fname;
        $this->avatar = $arr->avatar;
        $this->email = $arr->email;
        $this->siol = $arr->siol ?? 0;
        $this->user_nick = $arr->user_nick;
        $this->uncustomed = ProfUnEmper($user_id);
        $this->mode = $arr->mode;
        $this->agent = get_browser(null, true);
        $this->ismobiledevice = $this->agent['ismobiledevice'];
        return true;
    }

    function ByIdenty(string $identy = '')
    {
        $userinfo_arr = false;
        if(is_bot())
        {
            $identy = 'oJOffNqzrQZY';
        }

        if(empty($identy))
        {
            if(empty($_COOKIE['identy']))
                return false;
            $identy = OnlyText($_COOKIE['identy']);

            if(iconv_strlen($identy) != 12)
                return false;
        }

        $qwe = qwe("
		SELECT `mailusers`.*,
		`user_servers`.`server`,
		`servers`.`server_group`,
		`servers`.`server_name`
		FROM
		`mailusers`
		LEFT JOIN `user_servers` ON `user_servers`.`user_id` = `mailusers`.`mail_id`
		LEFT JOIN `servers` ON `servers`.`id` = `server`
		WHERE BINARY `mailusers`.`identy` = '$identy'
		");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        self::ByQwe($q);

        $this->uncustomed = ProfUnEmper($this->id);
        $this->agent = get_browser(null, true);
        $this->ismobiledevice = $this->agent['ismobiledevice'];
        return true;
    }

    public function ByQwe(object $q)
    {
        $this->age = $q->age;
        $this->avafile = $q->avafile;
        $this->avatar = $q->avatar;
        $this->email = $q->email ?? false;
        $this->first_ip = $q->ip;
        $this->first_name = $q->first_name;
        $this->fname = $q->first_name ?? 'Незнакомец';
        $this->id = $q->mail_id;
        $this->identy = $q->identy;
        $this->last_ip = $q->last_ip;
        $this->last_name = $q->last_name;
        $this->last_time = $q->last_time;
        $this->mailnick = $q->mailnick;
        $this->mode = $q->mode ?? 1;
        $this->server = $q->server ?? 9;
        $this->server_group = $q->server_group ?? 2;
        $this->siol = intval($q->siol);
        $this->time = $q->time;
        $this->token = $q->token;
        $this->user_id = $q->mail_id;
        $this->user_nick = $q->user_nick;

        if($q->email) {
            if(!$q->user_nick)
                $this->user_nick = NickAdder($q->mail_id);
            else
                $this->user_nick = $q->user_nick;
        }
    }

    public function IniAva()
    {
        if($this->avafile and file_exists($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$this->avafile)) {
            $this->avatar = 'img/avatars/' . $this->avafile;
            return $this->avatar;
        }


        include_once($_SERVER['DOCUMENT_ROOT'].'/../functions/filefuncts.php');
        //Пробуем получить с мыла по ссылке
        $avafile = AvaGetAndPut($this->avatar,$this->identy);
        if($avafile) {
            $this->avatar = 'img/avatars/'.$avafile;
            return $this->avatar;
        }


        $this->avatar = 'img/8001096.png';
        return $this->avatar;
    }
}