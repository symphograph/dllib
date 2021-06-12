<?php


class Freshness
{
    public function __construct(
        public int $fresh_group = 0,
        public int $fresh_type = 0,
        public int $fresh_lvl = 0,
        public int $fresh_tstart = 0,
        public int $fresh_tstop = 0,
        public int $fresh_per = 0,
        public string $fresh_name = ''
    )
    {
        $args = get_defined_vars();
        foreach ($args as $ak => $av) {
            $this->$ak = $av;
        }
    }

    public function option(int $item_id = 0)
    {
        if(($this->fresh_tstart/60) >= 24)
            $format = "jд. H:i";
        else
            $format = "H:i";

        $per = '';
        if($item_id)
            $per = ' '.$this->fresh_per.'%';

        $pack_time = date($format,$this->fresh_tstart*60-3600*3-3600*24);
        return "<option value='$this->fresh_tstart'>$pack_time $per</option>";
    }

    public function byAge(int $item_id, int $from_id, int $age)
    {
        $qwe = qwe("
        SELECT 
        fresh_data.fresh_tstart,
        fresh_data.fresh_tstop,
        fresh_data.fresh_per,
        fresh_data.fresh_lvl,
        fresh_data.fresh_group,
        fresh_data.fresh_type,
        fl.fresh_name       
        FROM
        packs
        INNER JOIN pack_prices ON pack_prices.item_id= packs.item_id AND packs.item_id = '$item_id'
        INNER JOIN zones ON zones.zone_id = pack_prices.zone_id AND pack_prices.zone_id = '$from_id'
        INNER JOIN pack_types ON packs.pack_t_id = pack_types.pack_t_id AND pack_types.pack_t_id != 6
        INNER JOIN fresh_data ON pack_types.fresh_group = fresh_data.fresh_group
        INNER JOIN fresh_lvls fl on fresh_data.fresh_lvl = fl.fresh_lvl
        AND zones.fresh_type = fresh_data.fresh_type
        AND '$age' between fresh_data.fresh_tstart and fresh_data.fresh_tstop
        GROUP BY fresh_data.fresh_tstart");
        if(!$qwe or !$qwe->num_rows){
            printr(get_defined_vars());
            return false;

        }

        $q = mysqli_fetch_object($qwe);
        foreach ($q as $k => $v)
        {
            if(!empty($v))
                $this->$k = $v;
        }
        return true;

    }


}