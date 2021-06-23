<?php


class Freshness
{
    const conditions = [
        1 => [
            'новый',
            'свежий',
            'подержанный',
            'поврежденный',
            'недоукомплектованный'
        ],
        2 => [
            'новый',
            'выдержанный',
            'подержанный',
            'испорченный',
            'недоукомплектованный'
        ],
        3 => [
            'новый',
            'подержанный',
            'поврежденный',
            'испорченный'
        ]
    ];
    public function __construct(
        public int $fresh_group = 0,
        public int $fresh_type = 0,
        public int $fresh_lvl = 0,
        public int $fresh_tstart = 0,
        public int $fresh_tstop = 0,
        public int $fresh_per = 0,
        public string $fresh_name = '',
        public int $condType = 0,
        public string $fperdata = '',
        public array $perdata = [],
        public int $fresh_id = 0
    )
    {
        $args = get_defined_vars();
        foreach ($args as $ak => $av) {
            $this->$ak = $av;
        }
        if($fresh_id){
            self::bydb();
        }
        $this->perdata = self::fPerData();
    }

    private function bydb()
    {

    }

    private function fPerData() : array
    {
        $arr = explode('|',$this->fperdata);
        if(!count($arr)){
            return [];
        }

        $perdata = [];
        foreach ($arr as $k =>$v){
            $perdata[$k+1] = $v;
        }

       return $perdata;
    }

    private function highLvl() : int
    {
        $per = max($this->perdata);
        return  array_search($per,$this->perdata);
    }

    private function downLvl() : int
    {
        $per = min($this->perdata);
        return  array_search($per,$this->perdata);
    }

    /**
     * @uses  downLvl()
     * @uses  highLvl()
     */
    public function setCondition(int $condition = 1)
    {
        $cond = ['','high','down'][$condition] ?? '';
        if(empty($cond)){
            return false;
        }

        $func = $cond.'Lvl';
        if(!method_exists($this,$func)){
            return false;
        }
        $lvl = self::$func();
        if(!$lvl){
            return false;
        }

        self::setLvl($lvl);
        return true;
    }

    public function setLvl(int $lvl) : bool
    {
        if(!count($this->perdata)){
            return false;
        }
        if(!isset($this->perdata[$lvl])){
            return false;
        }

        $this->fresh_per = $this->perdata[$lvl];
        $this->fresh_lvl = $lvl;
        $this->fresh_name = self::conditions[$this->condType][$lvl-1] ?? 'не определено';

        return true;
    }

    public function fPerOptions(){
        $arr = self::fPerData();

        foreach ($arr as $lvl => $per){
            $freshName = self::conditions[$this->condType][$lvl-1] ?? 'не определено';
            ?><option value="<?php echo $lvl?>"><?php echo $per . '% '.$freshName?></option><?php
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

}