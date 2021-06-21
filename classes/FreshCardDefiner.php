<?php


class FreshCardDefiner
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
    private array     $inputCard;
    private array     $packNames;
    private array $doodNames;
    private array $freshTypes;
    public FreshCard $Card;
    public Pack $Pack;

    public function __construct(array $packNames, array $doodNames, array $inputCard, array $freshTypes, $file)
    {
        $this->inputCard = $inputCard;
        $this->packNames = $packNames;
        $this->doodNames = $doodNames;
        $this->freshTypes = $freshTypes;

        $this->Card = new FreshCard();


        if(self::defPack()){
            $this->Pack = new Pack();
            $this->Pack->getFromDB($this->Card->item_id);
            //printr($this->Pack);
        }
        self::defFreshTypeName();
        self::defCondition();
        self::defFreshPer();
        self::defBeforeNext();
        if ($this->Card->beforeNext and $this->Card->fresh_lvl == 5){
            $this->Card->fresh_lvl = 0;
            $this->Card->condition = 'неизвестно';
        }
        self::defMaster();
        self::defOwner();
        $this->Card->datetime = $inputCard['datetime'];
        $this->Card->file = $file;
        $this->Card->insertTodb();

        return true;
    }

    private function defPack() : bool
    {
        foreach ($this->inputCard as $k => $v){
            $str = preg_replace(['/л ил йот/'], ['лилиот'], $v);
            $str = $str.'';
            if(in_array($str,$this->packNames)){
                $this->Card->item_id = array_search($str,$this->packNames);
                $this->Card->packName = $str;
                return true;
            }
        }

        foreach ($this->inputCard as $k => $v){
            $str = preg_replace(['/л ил йот/'], ['лилиот'], $v);
            $str = $str.'';
            if(in_array($str,$this->doodNames)){
                $this->Card->item_id = array_search($str,$this->doodNames);
                $this->Card->packName = $str;
                return true;
            }
        }
        return false;
    }

    private function defFreshTypeName() : bool
    {
        if(in_array($this->Card->item_id,[43323,43324,21,22])){
            $this->Card->freshTypeName = 'растворный';
            $this->Card->fresh_id = 11;
            return true;
        }
        foreach ($this->inputCard as $k => $v){
            if (!str_contains($v, 'товар'))
                continue;
            if (str_contains($v, 'фактории'))
                continue;
            if (str_contains($v, 'региональный'))
                continue;
            if (str_contains($v,'товар исчезает через')){
                continue;
            }
            $this->Card->tmp = $v;

            $narr = explode(' ',$v);

            $name = strPrepare($narr[1]);
            if(!in_array($name,$this->freshTypes)){
                return false;
            }
            $this->Card->fresh_id      = array_search($name,$this->freshTypes);
            $this->Card->freshTypeName = $name;
            return true;

        }

        return false;
    }

    private function defCondition()
    {
        foreach ($this->inputCard as $k => $v) {

            if (!str_contains($v, 'товар'))
                continue;
            if (str_contains($v, 'региональный'))
                continue;
            if (str_contains($v,'товар исчезает через')){
                continue;
            }
            if(!isset($this->Pack->condType)){
                continue;
            }
            foreach (self::conditions[$this->Pack->condType] as $fk => $fn){
                if(str_contains($v,$fn)){
                    $this->Card->condition = $fn;
                    $this->Card->fresh_lvl = $fk+1;
                    return true;
                }
            }
        }
        return false;
    }

    private function defFreshPer() : bool
    {
        foreach ($this->inputCard as $k => $v){
            if(!str_starts_with($v,'цена')){
                continue;
            }

            $freshPer = explode('цена',$v)[1] ?? 0;

            if(!$freshPer){
                return false;
            }
            $freshPer = trim($freshPer);
            if(str_ends_with($freshPer,'%')){

                $freshPer = str_replace(['%','+',':'],['','',''],$freshPer);
                $this->Card->fresh_per = $freshPer;
                return true;
            }
        }
        return false;
    }

    private function defBeforeNext() : bool
    {
        $interval = 15 * 60;
        foreach ($this->inputCard as $k => $v){
            if(!str_starts_with($v,'срок годности')){
                continue;
            }

            $str = explode('срок годности',$v)[1] ?? '';
            if(empty($str)){
                return false;
            }
            $str = trim($str);

            $str = str_replace(['<','д.','ч.','мин.','сек.',':'],['','day','hour','min','sec',''],$str);
            $str = trim($str);
            $str = str_replace(' ','',$str);
            //printr($str);

            $time = strtotime($str,0);
            //printr($time/60);
            $time = ceil($time / $interval) * $interval;
            $this->Card->beforeNext = $time/60;
            //printr($this->Card->beforeNext);

        }
        return false;
    }

    private function defMaster()
    {
        foreach ($this->inputCard as $k => $v){
            if(!str_starts_with($v,'мастер')){
                continue;
            }

            $master = explode('мастер',$v)[1] ?? 0;

            if(!$master){
                return false;
            }
            $master = trim($master);

            $master = str_replace(['-',':'],['',''],$master);
            $master = trim($master);
            $master = OnlyText($master);
            $this->Card->master = $master;
            return true;
        }
        return false;
    }

    private function defOwner()
    {
        foreach ($this->inputCard as $k => $v){
            if(!str_starts_with($v,'хозяин')){
                continue;
            }

            $owner = explode('хозяин',$v)[1] ?? 0;

            if(!$owner){
                return false;
            }
            $owner = trim($owner);

            $owner = str_replace(['+',':'],['',''],$owner);
            $owner = trim($owner);
            $this->Card->owner= $owner;
            return true;
        }
        return false;
    }
}