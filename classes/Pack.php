<?php


class Pack extends Item
{
    public int $from = 0;
    public int $to = 0;
    public int $tid = 0;
    public string $tname = 'Тип не определен';

    public int $facprice = 0;

    public function getFromDB(int $item_id)
    {
        parent::getFromDB($item_id);

        if(!$this->ispack)
            return false;

        $qwe = qwe("
        SELECT * FROM packs 
        INNER JOIN pack_types pt 
            ON packs.pack_t_id = pt.pack_t_id 
            AND packs.pack_type = pt.pack_t_name
            AND packs.item_id = '$item_id'
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

    }
}