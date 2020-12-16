<?php


class Pack extends Item
{
    public int $z_from = 0;
    public string $z_from_name = 'Откуда';
    public int $z_to = 0;
    public string $z_to_name = 'Куда';
    public int $tid = 0;
    public string $tname = 'Тип пака';
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
        INNER JOIN zones z on packs.zone_id = z.zone_id
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);
        if(self::byQwe($q))
            return true;

        return false;
    }

    public function byQwe(object $q): bool
    {
        parent::byQwe($q);

        $this->slot = 19;
        $this->tid = $q->pack_t_id;
        $this->tname = $q->pack_t_name;
        $this->z_from = $q->zone_id ?? 0;
        $this->z_from_name = $q->zone_name ?? 'Откуда';

        return true;
    }
}