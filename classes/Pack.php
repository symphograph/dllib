<?php


class Pack extends Item
{
    public int            $zone_from   = 0;
    public string|null    $z_from_name = 'Откуда';
    public int            $zone_to     = 0;
    public string         $z_to_name   = 'Куда';
    public int            $pack_t_id   = 0;
    public string         $pack_t_name = 'Тип пака';
    public int            $fact_price  = 0;
    public int            $pack_price  = 0;
    public string         $pack_sname  = '';
    public string         $pack_name   = '';
    public int            $fresh_per   = 0;
    public int            $fresh_type  = 0;
    public int            $fresh_group = 0;
    public Freshness $Fresh;
    public int            $age         = 0;
    public array          $Zones       = [];
    public int            $pass_labor  = 0;
    public int            $valuta_id;
    public PackPrice      $PackPrice;
    public int $condType = 0;

    public function __construct()
    {
        $this->Fresh = new Freshness();
    }


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
            AND packs.native_id = '$item_id'
        INNER JOIN zones z on packs.zone_from = z.zone_id
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
        self::reSname();
        return true;
    }

    public function reSname()
    {
        $pack_name = $this->pack_sname ?? $this->item_name;
        if(preg_match('/Груз компоста/',$pack_name))
            $this->pack_name = 'Груз компоста';
        if(preg_match('/Груз зрелого сыра/',$pack_name))
            $this->pack_name = 'Груз сыра';
        if(preg_match('/Груз домашней наливки/',$pack_name))
            $this->pack_name = 'Груз наливки';
        if(preg_match('/Груз меда/',$pack_name))
            $this->pack_name = 'Груз меда';
        if($pack_name == 'Вяленые припасы Заболоченных низин')
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
    public function freshGet(int $age)
    {
        $this->age = $age;
        $Fresh = new Freshness();
        $Fresh->byAge($this->item_id, $this->zone_from,$this->age);
        $this->Fresh = $Fresh;
    }

    public function bestcondition(){
        $Fresh = new Freshness();
    }

    public function printRow(int $per,int $siol) : string
    {
        parent::isCounted();
        //printr($this->bestCraft);

        $this->PackPrice = new PackPrice(
            per: $per,
            siol: $siol,
            item_id: $per,
            db_price: $this->pack_price,
            fresh_per: $this->Fresh->fresh_per,
            valut_id: $this->valuta_id,
            craft_price: $this->craft_price,
            labor_total: $this->bestCraft->labor_total
        );

        ob_start();
        ?>
        <div class="piconandpname">
            <div itid="<?php echo $this->item_id ?>" id="<?php echo $this->item_id . '_' . $this->zone_to ?>"
                 class="pack_icon" style="background-image: url(img/icons/50/<?php echo $this->icon ?>.png)">
                <div class="itdigp"><?php echo $this->Fresh->fresh_per; ?>%</div>
            </div>

            <div id="pmats_<?php echo $this->item_id ?>" class="pkmats_area"></div>

            <div class="pack_name">
                <div class="pack_mname"><b><?php echo $this->pack_name; ?></b></div>

                <div class="znames">
                    <div class="znamesrows">
                        <div class="zname"></div>
                        <div class="zname"><?php echo $this->z_from_name ?></div>
                        <div class="zname"></div>
                    </div>
                    <div class="znamesrows">
                        <div class="zname2"></div>
                        <div class="zname2"></div>
                        <div class="zname2"><?php echo $this->z_to_name ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pprices">
            <?php $this->PackPrice->printPriceData();?>
        </div>
        <?php
        return ob_get_clean();
    }
}