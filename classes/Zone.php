<?php


class Zone
{
    public int $zone_id = 0;
    public string $zone_name= 'Локация';
    public $side = 0;
    public int $is_get = 0;
    public int $get_west = 0;
    public int $get_east = 0;
    public int $fresh_type = 0;

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
        if(!$qwe or !$qwe->num_rows){
            return false;
        }
        $q = mysqli_fetch_object($qwe);

        if(!self::byQ($q)){
            return false;
        }

        return true;
    }

    public function byQ(object|array $q)
    {
        foreach ($q as $qk => $kv){
            if(!empty($kv)){
                $this->$qk = $kv;
            }
        }
        return true;
    }
}