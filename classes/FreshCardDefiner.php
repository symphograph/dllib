<?php


class FreshCardDefiner
{
    const conditions = [
        'новый'                => 1,
        'свежий'               => 2,
        'выдержанный'          => 2,
        'подержанный'          => 3,
        'поврежденный'         => 4,
        'недоукомплектованный' => 5,
    ];
    private array     $inputCard;
    private array     $packNames;
    private array $freshTypes;
    public FreshCard $Card;

    public function __construct(array $packNames, array $inputCard, array $freshTypes)
    {
        $this->inputCard = $inputCard;
        $this->packNames = $packNames;
        $this->freshTypes = $freshTypes;

        $this->Card = new FreshCard();
        printr($inputCard);

        self::defPack();
        self::defFreshTypeName();
        self::defFreshPer();
        return true;
    }

    private function defPack() : bool
    {
        foreach ($this->inputCard as $k => $v){
            //if (!str_starts_with($v,'груз'))
               // continue;
            $str = preg_replace(['/груз обработанного/','/груз зубного порошка/'], ['груз','зубной порошок'], $v);
            if(in_array($str,$this->packNames)){
                $this->Card->item_id = array_search($str,$this->packNames);
                $this->Card->packName = $str;
                return true;
            }
        }
        return false;
    }

    private function defFreshTypeName() : bool
    {
        foreach ($this->inputCard as $k => $v){
            if(str_contains($v,'товар') and !str_contains($v,'региональный')){
                $narr = explode(' ',$v);
                //printr($narr);
                $name = $narr[1];
                $condition = $narr[0];
                if(!in_array($name,$this->freshTypes)){
                    return false;
                }
                $this->Card->freshTypeId = array_search($name,$this->freshTypes);
                $this->Card->freshTypeName = $name;
                $this->Card->condition = $condition;
                if(isset(self::conditions[$condition])){
                    $this->Card->fresh_lvl = self::conditions[$condition];
                }
                return true;
            }
        }
        return false;
    }

    private function defFreshPer() : bool
    {
        foreach ($this->inputCard as $k => $v){
            if(str_starts_with($v,'цена:')){
                $freshPer = explode(':',$v)[1] ?? 0;
                if(!$freshPer){
                    return false;
                }
                $freshPer = trim($freshPer);
                if(str_ends_with($freshPer,'%')){
                    $freshPer = str_replace('%','',$freshPer);
                    $this->Card->fresh_per = $freshPer;
                    return true;
                }
            }
        }
        return false;
    }
}