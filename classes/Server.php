<?php


class Server
{
    public int $id = 9;
    public string $name = 'Сервер?';
    public int $group = 2;

    public function __construct($user_id)
    {
        $qwe = qwe("SELECT `server`, `server_group`, server_name 
        FROM `user_servers`
        INNER JOIN `servers` 
        ON `user_servers`.`server` = `servers`.`id`
        AND `user_servers`.`user_id` = '$user_id'
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->id = $q->server;
        $this->name = $q->server_name;
        $this->group = $q->server_group;
    }
}