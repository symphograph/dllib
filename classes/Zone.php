<?php


class Zone
{
    private $side = 0;
    public int $zone_id = 0;
    public string $zone_name= 'Локация';

    public array $zonesTo = [];
    private int|null $is_get = 0;
    private int|null $get_west = 0;
    private int|null $get_east = 0;
    private int|null $fresh_type = 0;


    public function __construct(int $zone_id = 0)
    {
        if(!$zone_id){
            return false;
        }
        if(!self::byId($zone_id)){
            return false;
        }
        return true;
    }

    public function byId(int $zone_id) : bool
    {
        $qwe = qwe("SELECT * FROM zones WHERE zone_id = '$zone_id'");
        if(!$qwe or !$qwe->rowCount()){
            return false;
        }
        $q= $qwe->fetchObject();

        if(!self::byQ($q)){
            return false;
        }

        return true;
    }

    public function byQ(object|array $q)
    {
        $q = (object) $q;
        foreach ($this as $key => $val){
            if(isset($q->$key) && !empty($q->$key)){
                $this->$key = $q->$key;
            }
        }
        return true;
    }

    public function initZonesTo() : array
    {
        $qwe = qwe("
        SELECT 
        pack_prices.zone_to as zone_id,
        zones.zone_name
        FROM pack_prices
        INNER JOIN zones ON pack_prices.zone_to = zones.zone_id
        AND pack_prices.zone_id = :zone_id
        GROUP BY pack_prices.zone_id, pack_prices.zone_to
        ",['zone_id'=>$this->zone_id]);
        if(!$qwe or !$qwe->rowCount()){
            return [];
        }
        $arr= $qwe->fetchAll(PDO::FETCH_CLASS, "Zone");

        foreach ($arr as $a){
            $this->zonesTo[$a->zone_id] = $a;
        }
        //$this->zonesTo[0] = ['zone_id'=>0,'zone_name'=>'Вcе лок'];
        $this->zonesTo[0] = new Zone();
        $this->zonesTo[0]->zone_name = 'Все локации';
        //printr($this->zonesTo);

        return $this->zonesTo;
    }
}