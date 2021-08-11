<?php

class Side
{
    const Sides = [
        1=>'Запад',
        2=>'Восток',
        3=>'Север',
        9=>'Остров свободы'
    ];
    public string $sideName = 'Материк';
    public array $zones = [];
    public array $zonesTo = [];

    public function __construct(private int $side = 0)
    {
        if(!isset(self::Sides[$side])){
            return false;
        }
        $this->sideName = self::Sides[$side];
    }

    public static function initZones(int $side) : array
    {
        $qwe = qwe("
        SELECT * FROM zones 
        WHERE fresh_type
        AND side = :side
        ORDER BY zone_name
        ",['side'=>$side]);
        if(!$qwe or !$qwe->rowCount()){
            return [];
        }

        return $qwe->fetchAll(PDO::FETCH_CLASS, "Zone");
    }

    public function getZonesForSelect() : array
    {

        if(!count($this->zones)){
            $this->zones = self::initZones($this->side);
        }
        if(!count($this->zones)){
            return [];
        }
        //printr($this->zones);


        $arr = $arrAll = [];
        //$this->zones
        foreach ($this->zones as $z) {
            $z->initZonesTo();
            $arr[]  = $z;
            $arrAll = array_merge($arrAll, $z->zonesTo);
        }

        $arrAll = array_unique($arrAll, SORT_REGULAR);
        $aa     = [];

        foreach ($arrAll as $a) {
            $aa[$a->zone_id] = $a;
        }
        $arrAll = $aa;
        $arrAll = ['zone_id' => 0, 'zone_name' => 'Весь материк', 'zonesTo' => $arrAll];
        array_unshift($arr, $arrAll);

        return $arr;

        //return array_column($this->zones,'zone_name','zone_id');

    }


}