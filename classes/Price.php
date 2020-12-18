<?php


class Price
{
    const  COLORS = [
        '',
        '#f35454',
        '#dcde4f',
        '#79f148'
    ];

    public int $item_id = 0;
    public int $price = 0;
    public string $time = '2020-02-22';
    public int $autor = 0;
    public string $how = 'Неизвестно';
    public string $color = '';

    public function byMode($item_id) : bool
    {
        global $User;
        if(!isset($User))
            die('Missed User');

        $this->how = 'Цена пользователя';
        if($User->mode == 1) {
            //Максимально широко.
            return self::Mode1($item_id);
        }

        if($User->mode == 2) {
            //В пределах друзей.
            return self::Mode2($item_id);
        }

        if($User->mode == 3) {
            //Только у себя.
            return self::Solo($item_id);
        }

        die('Missed mode');
    }

    public function Mode1(int $item_id) : bool
    {

        if(in_array($item_id,IntimItems())) {
            if(self::Solo($item_id))
                return true;
        }

        if(self::withFrends($item_id))
            return true;

        if(self::fromGood($item_id))
            return true;

        return self::fromAny($item_id);
    }

    public function Mode2(int $item_id) : bool
    {

        if(in_array($item_id,IntimItems())) {
            if(self::Solo($item_id))
                return true;
        }

        return self::withFrends($item_id);
    }

    public function Solo(int $item_id) : bool
    {
        global $User;

        $qwe = qwe("
            SELECT `auc_price`,`time` FROM `prices`
            WHERE `user_id` = '$User->id' 
            AND `item_id` = '$item_id'
            AND `server_group` = '$User->server_group'
            ");
        if($qwe and $qwe->num_rows) {

            $q = mysqli_fetch_object($qwe);

            $this->autor = $User->id;
            $this->price = $q->auc_price;
            $this->time = $q->time;
            return true;
        }

        if(IsValuta($item_id))
            return false;

        if(!in_array($item_id,IntimItems()))
            return false;


        $arr =  ItemAny($item_id,'price_sale');
        if(!$arr)
            return false;


        $price = array_shift($arr);
        $price = intval($price);
        if(!$price)
            return false;


        $this->price = $price;
        $this->autor = 1;
        $this->time = '2020-02-22';

        return true;
    }

    public function withFrends(int $item_id) : bool
    {
        global $User;
        //Хотим цены только от друзей или себя.
        //Друзей предпочитаем, если цена новее.
        //Выясняем друзей.

        if(!$User->folows())
            return self::Solo($item_id);


        $folows = implode(',',$User->folows);

        $qwe = qwe("
            SELECT `auc_price`, `user_id`,`time`
            FROM `prices`
            WHERE `user_id` in ( $folows )
            AND `item_id` = '$item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC 
            LIMIT 1");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    public function fromGood(int $item_id) : bool
    {
        //ищем у юзеров, чьи записи преимущественно адеквадны
        global $User;

        $qwe = qwe("
            SELECT 
            `prices`.`auc_price`, 
            `prices`.`user_id`,
            `prices`.`time` 
            FROM `prices`
            INNER JOIN folows 
            ON (`prices`.`user_id` = folows.folow_id AND folows.user_id = 893) 
            OR (`prices`.`user_id` = 893 AND folows.user_id = `prices`.`user_id`)
            WHERE `item_id` = '$item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC
            LIMIT 1");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    public function fromAny(int $item_id) : bool
    {
        //ищем у кого угодно. Лишь бы найти.
        global $User;

        $qwe = qwe("
            SELECT `auc_price`, `user_id`,`time` FROM `prices` 
            WHERE `item_id` = '$item_id'
            AND `server_group` = '$User->server_group'
            ORDER BY `time` DESC 
            LIMIT 1");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);

        $this->price = $q->auc_price;
        $this->autor = $q->user_id;
        $this->time = $q->time;

        return true;
    }

    public function byCraft(int $item_id) : bool{

        global $User;

        $qwe = qwe("
            SELECT `craft_price`, `updated` FROM `user_crafts` 
            WHERE `user_id` = '$User->id' 
            AND `item_id` = '$item_id'
            AND `isbest` in (1,2)
            ORDER BY `isbest` DESC
            LIMIT 1
            ");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q = mysqli_fetch_object($qwe);
        if(!$q->craft_price)
            return false;

        $this->price = $q->craft_price;
        $this->autor = $User->id;
        $this->time = $q->updated;
        $this->how  = 'Себестоимость (крафт)';
        return true;
    }

    function getColor(): bool
    {
        if(!$this->price){
            $this->color = self::COLORS[1];
            return true;
        }


        global $User;

        if($User->id == $this->autor){
            $this->color = self::COLORS[3];
            return true;
        }


        $User->folows();
        if(in_array($this->autor,$User->folows)){
            $this->color = self::COLORS[2];
            return true;
        }


        $this->color = self::COLORS[1];
        return true;
    }

}