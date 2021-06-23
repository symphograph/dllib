<?php


class Pack extends Item
{
    public int         $zone_from   = 0;
    public string|null $z_from_name = 'Откуда';
    public int         $zone_to     = 0;
    public string      $z_to_name   = 'Куда';
    public int         $pack_t_id   = 0;
    public string      $pack_t_name = 'Тип пака';
    public int         $fact_price  = 0;
    public int         $pack_price  = 0;
    public string      $pack_sname  = '';
    public string      $pack_name   = '';
    public int         $fresh_per   = 0;
    public int         $fresh_type  = 0;
    public int         $fresh_group = 0;
    public Freshness   $Fresh;
    public int         $age         = 0;
    public array       $Zones       = [];
    public int         $pass_labor  = 0;
    public int        $valuta_id = 500;
    public PackProfit $PackProfit;
    public Salary     $Salary;
    public int         $condType    = 0;
    public string      $fperdata;
    public int         $fresh_id;

    public function __construct()
    {
        $this->Fresh = new Freshness();
    }


    public function getFromDB(int $item_id, int $zfrom_id = 0, int $zto_id = 0)
    {
        parent::getFromDB($item_id);


        if(!$this->ispack)
            return false;

        if($zfrom_id and $zto_id){

            return self::byWay($item_id,$zfrom_id,$zto_id);

        }

        $qwe = qwe("
        SELECT * FROM packs 
        INNER JOIN pack_types pt 
            ON packs.pack_t_id = pt.pack_t_id 
            AND packs.pack_type = pt.pack_t_name
            AND packs.item_id = '$item_id'
        INNER JOIN zones z on packs.zone_from = z.zone_id
        INNER JOIN fresh_types ft on packs.fresh_id = ft.id
        ");
        if(!$qwe or !$qwe->num_rows){
            return false;
        }

        $q = mysqli_fetch_object($qwe);
        if(self::byQ($q))
            return true;

        return false;
    }

    public function byQ(object|array $q): bool
    {

        $q = (object) $q;


        //parent::byQ($q);

        foreach ($q as $qk => $kv){
            if(!empty($kv)){
                $this->$qk = $kv;
            }
        }
        $this->z_from_name = (new Zone($this->zone_from))->zone_name;
        $this->z_to_name = (new Zone($this->zone_to))->zone_name;
        $this->Fresh = new Freshness(
            condType: $q->condType ?? 0,
            fperdata: $q->fperdata ?? ''
        );

        self::reSname();
        return true;
    }

    public function fPerOptions()
    {
        $this->Fresh->fPerOptions();
    }

    private function reSname()
    {
        if($this->pack_sname){
            $this->pack_name = $this->pack_sname;
        }else{
            $this->pack_name = $this->item_name;
        }

        if(preg_match('/Груз компоста/',$this->pack_name))
            $this->pack_name = 'Груз компоста';
        if(preg_match('/Груз зрелого сыра/',$this->pack_name))
            $this->pack_name = 'Груз сыра';
        if(preg_match('/Груз домашней наливки/',$this->pack_name))
            $this->pack_name = 'Груз наливки';
        if(preg_match('/Груз меда/',$this->pack_name))
            $this->pack_name = 'Груз меда';
        if($this->pack_name == 'Вяленые припасы Заболоченных низин')
            $this->pack_name = 'Вяленые припасы';

    }

    /*
    public function freshPerces() : array
    {
        if(count($this->freshPerces)){
            return $this->freshPerces;
        }


        $qwe = qwe("SELECT * FROM fresh_data 
        WHERE fresh_type = '$this->fresh_type' 
        and fresh_group = '$this->fresh_group'
        ");
        if(!$qwe or !$qwe->num_rows)
            return [];

        foreach ($qwe as $q){
            $this->freshPerces[] = $q['fresh_per'];
        }
        return $this->freshPerces;
    }
*/

    public function initSalary(int $per,int $siol, int $quality = 0, $lvl = 0) : void
    {
        if($quality){
            $this->Fresh->setCondition($quality);
        }elseif ($lvl){
            $this->Fresh->setLvl($lvl);
        }

        $this->Salary = new Salary(
            per: $per,
            siol: $siol,
            db_price: $this->pack_price,
            fresh_per: $this->Fresh->fresh_per,
            valut_id: $this->valuta_id
        );
    }

    public function initPrice(int $per,int $siol, int $quality = 0, $lvl = 0) : void
    {

        self::initSalary(per: $per,siol:  $siol, quality:  $quality, lvl:  $lvl);

        if(!parent::isCounted()){
            return;
        }
        $this->getBestCraft();
        $this->bestCraft->setCountedData();


        $this->PackProfit = new PackProfit(
            finalSalary: $this->Salary->finalSalary,
            valut_id: $this->valuta_id,
            craft_price: $this->craft_price,
            labor_total: $this->bestCraft->labor_total
        );
    }

    private function byWay(int $item_id, int $zfrom_id, int $zto_id) : bool
    {
        global $User;
        $qwe = qwe("SELECT 
            items.*,
            pack_prices.zone_to, 
            pack_prices.pack_price, 
            pack_prices.valuta_id, 
            pack_prices.mul, 
            packs.zone_from, 
            packs.pack_sname, 
            pt.pack_t_id, 
            pt.pack_t_name, 
            pt.pass_labor,     
            pt.fresh_group, 
            ft.fperdata,
            ft.condType       
            FROM packs
            INNER JOIN pack_prices ON packs.item_id = pack_prices.item_id
            AND packs.zone_from = pack_prices.zone_id
            AND pack_prices.zone_id = '$zfrom_id'
                AND pack_prices.zone_to = '$zto_id'
                AND packs.item_id = '$item_id'
            INNER JOIN items ON packs.item_id = items.item_id AND items.on_off
            INNER JOIN pack_types pt on packs.pack_t_id = pt.pack_t_id
            INNER JOIN fresh_types ft on packs.fresh_id = ft.id
            ORDER BY packs.item_id");

        if(!$qwe or !$qwe->num_rows){

            return false;
        }
        $q = mysqli_fetch_object($qwe);

        return self::byQ($q);
    }

}