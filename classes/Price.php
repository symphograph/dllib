<?php


class Price
{

    public int $item_id = 0;
    public int $price = 0;
    public string $time = '2020-02-22';
    public int $autor = 0;
    public int $val_id = 500;

    public function Solo(int $item_id,object $User)
    {

        $qwe = qwe("
            SELECT `auc_price`,`time` FROM `prices`
            WHERE `user_id` = '$User->id' 
            AND `item_id` = '$item_id'
            AND `server_group` = '$User->server_group'
            ");
        if($qwe and $qwe->num_rows) {

            $q = mysqli_fetch_object($qwe);

            $this->autor = $q->user_id;
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
}