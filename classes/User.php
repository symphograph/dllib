<?php


class User
{
    public int $user_id;
    public string $first_name;
    public string $last_name;
    public int $age;
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
    public int $mode;

    public function __construct(int $user_id)
    {
        $qwe = qwe("SELECT * FROM mailusers WHERE mail_id = '$user_id'");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->user_id = $user_id;
        $this->first_name = $q->first_name;
        $this->last_name = $q->last_name;
        $this->age = $q->age;
        $this->email = $q->email;
        $this->time = $q->time;
        $this->last_time = $q->last_time;
        $this->avatar = $q->avatar;
        $this->mailnick = $q->mailnick;
        $this->ip = $q->ip;
        $this->last_ip = $q->last_ip;
        $this->identy = $q->identy;
        $this->token = $q->token;
        $this->siol = $q->siol;
        $this->user_nick = $q->user_nick;
        $this->avafile = $q->avafile;
        $this->mode =  $q->mode;

        return true;
    }

}