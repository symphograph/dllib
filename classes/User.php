<?php


class User
{
    public int $id;
    public string $first_name;
    public string $last_name;
    public $age;
    public string $email;
    public string $time;
    public string $last_time;
    public string $avatar;
    public string $mailnick;
    public string $identy;
    public string $token;
    public int $siol;
    public string $user_nick;
    public string $avafile;
    public string $first_ip;
    public string $last_ip;
    public int $mode;
    public int $server = 9;
    public int $server_group = 2;
    public bool $uncustomed;
    public $ismobiledevice;
    public int $orcost = 250;

    public function getById(int $user_id)
    {
        $qwe = qwe("SELECT * FROM mailusers WHERE mail_id = '$user_id'");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->id = $user_id;
        $this->first_name = $q->first_name;
        $this->last_name = $q->last_name;
        $this->age = $q->age;
        $this->email = $q->email;
        $this->time = $q->time;
        $this->last_time = $q->last_time;
        $this->avatar = $q->avatar;
        $this->mailnick = $q->mailnick;
        $this->first_ip = $q->ip;
        $this->last_ip = $q->last_ip;
        $this->identy = $q->identy;
        $this->token = $q->token;
        $this->siol = $q->siol;
        $this->user_nick = $q->user_nick;
        $this->avafile = $q->avafile;
        $this->mode =  $q->mode;

        $Server = new Server($user_id);
        $this->server = $Server->id;
        $this->server_group = $Server->group;

        return true;
    }

    public function getByGlobal()
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

}