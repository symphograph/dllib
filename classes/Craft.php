<?php


class Craft
{
    public int $craft_id;
    public string|null $rec_name = 'Имя рецепта';
    public $dood_id;
    public $dood_name;
    public $result_item_id;
    public $result_item_name;
    public $labor_need;
    public $profession;
    public $prof_need;
    public int $result_amount;
    public $on_off;
    public $isbottom;
    public $dood_group;
    public $deep;
    public $my_craft;
    public $craft_time;
    public int|null $prof_id = 27;
    public int $grade = 1;
    public $mins;
    public int|null $spm;
    public array $mats = [];
    public int $isbest = 0;
    public int $orcost = 300;

    //ОР с учетом прокачки профы у юзера
    public int $labor_need2 = 0;

    //ОР на один рецепт с учетом прокачки профы у юзера
    public float $labor_single = 0;

    //ОР на всю цепочку крафта
    public float $labor_total = 0;

    public int $spmu = 0;
    public int $craft_price = 0;

    public function __construct(int $craft_id = 0)
    {
        if(!$craft_id)
            return false;

        $qwe = qwe("SELECT * FROM crafts WHERE on_off AND craft_id = '$craft_id'");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);


        $this->craft_id         = $q->craft_id;
        $this->rec_name         = $q->rec_name;
        $this->dood_id          = $q->dood_id;
        $this->dood_name        = $q->dood_name;
        $this->result_item_id   = $q->result_item_id;
        $this->result_item_name = $q->result_item_name;
        $this->labor_need       = $q->labor_need;
        $this->profession       = $q->profession;
        $this->prof_need        = $q->prof_need;
        $this->result_amount    = $q->result_amount;

        if($q->dood_id == 9131)
            $this->result_amount = $q->result_amount * 1.1;

        $this->on_off     = $q->on_off;
        $this->isbottom   = $q->isbottom;
        $this->dood_group = $q->dood_group;
        $this->deep       = $q->deep;
        $this->my_craft   = $q->my_craft;
        $this->craft_time = $q->craft_time;
        $this->prof_id    = $q->prof_id;
        $this->grade      = intval($q->grade);
        $this->mins       = $q->mins;
        $this->spm        = $q->spm;
        $this->mats       = self::getMats();
        return true;
    }

    public function getMats() : array
    {
        global $User;
        $mats = [];
        $qwe = qwe("
        SELECT craft_materials.* , 
               i.*,
               item_categories.item_group,
               uc.isbest
        FROM craft_materials
        INNER JOIN items i on craft_materials.item_id = i.item_id
        LEFT JOIN `item_categories` ON i.`categ_id` = `item_categories`.`id`
        LEFT JOIN user_crafts uc on craft_materials.item_id = uc.item_id 
                                        AND uc.user_id = '$User->id'
                                        AND uc.isbest > 0
        WHERE craft_materials.craft_id = '$this->craft_id'
        ");
        foreach ($qwe as $q)
        {
            $q   = (object)$q;
            $mat = new Mat();
            $mat->byQ($q);
            $mat->item_id    = $q->item_id;
            $mat->mater_need = $q->mater_need;
            $mat->need_grade = intval($q->mat_grade);

            if (!$mat->need_grade)
                $mat->need_grade = $q->basic_grade ?? 1;
            if ($q->basic_grade > $mat->need_grade)
                $mat->need_grade = $q->basic_grade;

            $mat->item_name = $q->item_name;
            $mat->craftId   = $this->craft_id;
            $mat->craftable    = $q->craftable;
            $mat->item_group   = $q->item_group ?? 0;
            $mat->icon         = $q->icon ?? '';
            $mat->isbest       = $q->isbest ?? 0;
            $mat->is_trade_npc = $q->is_trade_npc ?? 0;
            $mat->valut_id     = $q->valut_id ?? 500;
            if($q->isbest == 3){
                $mat->is_buyable = true;
            }

            $mats[$mat->item_id] = $mat;
        }
        return $mats;
    }

    public function InitForUser()
    {
        global $User;
        $Prof = new Prof();
        $Prof->InitForUser($this->prof_id);

        $labor_need2 = $this->labor_need*((100 - $Prof->save_or) * 0.01);
        $labor_need2 = round($labor_need2,0);

        $labor_single = $labor_need2 / $this->result_amount;
        $labor_single = round($labor_single,2);

        $this->labor_need2 = $labor_need2;
        $this->labor_single = $labor_single;


        $this->orcost = $User->orCost();
    }

    public function setCountedData(int $user_id): bool
    {
        $qwe = qwe("SELECT * FROM user_crafts 
        WHERE user_id = '$user_id'
        AND craft_id = '$this->craft_id'
        ") ;
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->isbest      = $q->isbest;
        $this->spmu        = $q->spmu;
        $this->craft_price = $q->craft_price;
        $this->labor_total = $q->labor_total;

        return true;
    }

    public function rescost() : array
    {
        global $User;
        $groupcraft = self::GroupCraft();
        if($groupcraft)
            return $groupcraft;




        //Запрашиваем про материалы
        $qwe = qwe(
            "SELECT 
        `items`.`craftable`, 
        `craft_materials`.`mater_need`,
       `craft_materials`.`mat_grade`,
        `items`.`item_name`, 
        `items`.`price_buy`, 
        `items`.`price_sale`, 
        `items`.`price_type`,
        `items`.valut_id,
        `items`.`is_trade_npc`, 
        `items`.`item_id` as `id`, 
        `items`.`forup_grade`,
        `items`.`categ_id`,
        `items`.`personal`,
        `user_crafts`.`isbest`,
        `user_crafts`.`craft_price`,
        craft_buffer2.spm*craft_materials.mater_need as spm2,
        craft_buffer2.craft_price as buffer_price
        FROM 
        `craft_materials`
        INNER JOIN `items`
        ON `items`.`item_id` = `craft_materials`.`item_id`
        AND `craft_materials`.`craft_id` = '$this->craft_id'
        AND `items`.`ismat`
        AND `items`.`on_off`
        LEFT JOIN `user_crafts` 
        ON `items`.`item_id` = `user_crafts`.`item_id`
        AND `user_crafts`.`user_id` = '$User->id'
        AND `user_crafts`.`isbest` > 0
        LEFT JOIN craft_buffer2 
        ON `craft_materials`.`item_id` = craft_buffer2.item_id
        AND craft_buffer2.user_id = '$User->id'
        ORDER BY `id`");
        $sum = $sumspm = 0;
        foreach($qwe as $q)
        {

            $mat = new Mat;
            $mat->byRcost($q);

            $spm2 = $mat->spm2;

            $mater_need = $mat->mater_need;
            if($mater_need == 0) continue;


            if($mater_need > 0)
                $sumspm = $sumspm + $spm2;

            $sum += ($mater_need * $mat->price);

        }
        //echo '<p>Ор-ов: '.$this->labor_need2.' по '.$this->orcost.'</p>';
        $crftprice = $sum + ($this->labor_need2 * $this->orcost);
        $crftprice = round($crftprice / $this->result_amount);
        $this->craft_price = $crftprice;
        $sumspm = round($sumspm / $this->result_amount);
        return [$crftprice,$sumspm];
    }

    public function GroupCraft()
    {
        global $User;

        $qwe = qwe("
        SELECT `item_name`, `amount`, sum(`amount`) as `sum`
        FROM `craft_groups` 
        WHERE `group_id` = 
        (SELECT `group_id` FROM `craft_groups` WHERE `craft_id` = '$this->craft_id')
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $gcr = mysqli_fetch_assoc($qwe);
        $am_sum = $gcr['sum'];
        if(!$am_sum ) return false;


        $qwe = qwe("SELECT 
        `items`.`craftable`, 
        `craft_materials`.`mater_need`,
       `craft_materials`.`mat_grade`,
        `items`.`item_name`, 
        `items`.`price_buy`, 
        `items`.`price_sale`, 
        `items`.`price_type`,
        `items`.valut_id,
        `items`.`is_trade_npc`, 
        `items`.`item_id` as `id`, 
        `items`.`forup_grade`,
        `items`.`categ_id`,
        `items`.`personal`,
        `user_crafts`.`isbest`,
        `user_crafts`.`craft_price`,
        craft_buffer2.spm*craft_materials.mater_need as spm2,
        craft_buffer2.craft_price as buffer_price
        FROM 
        `craft_materials`
        INNER JOIN `items`
        ON `items`.`item_id` = `craft_materials`.`item_id`
        AND `craft_materials`.`craft_id` = '$this->craft_id'
        AND `items`.`ismat`
        AND `items`.`on_off`
        LEFT JOIN `user_crafts` 
        ON `items`.`item_id` = `user_crafts`.`item_id`
        AND `user_crafts`.`user_id` = '$User->id'
        AND `user_crafts`.`isbest` > 0
        LEFT JOIN craft_buffer2 
        ON `craft_materials`.`item_id` = craft_buffer2.item_id
        AND craft_buffer2.user_id = '$User->id'
        ORDER BY `id`
        ");
        $sum = $sumspm = 0;
        foreach($qwe as $q)
        {

            if(!$q['mater_need'] or $q['mater_need'] < 0)
                continue;

            $mat = new Mat;
            $mat->byRcost($q);

            $sumspm += $mat->spm2;
            $sum += ($mat->mater_need * $mat->price);
        }

        $crftprice = $sum + ($this->labor_need2 * $this->orcost);
        $crftprice = round($crftprice / $am_sum);
        //echo '<br>'.$this->result_item_name.' '.$crftprice;
        $this->craft_price = $crftprice;
        $sumspm = round($sumspm / $this->result_amount);
        return [$crftprice,$sumspm];
    }

    public function MaCubiki(array $mats, int $u_amount): bool
    {

        $money = 0;
        $flowers = [2178, 3564, 3622,3627,3628,3659,3667,3671,3680,3684,3685,3711,3713,8009,14629,14630,14631,16268,16273,16290];
        foreach($mats as $Mat)
        {

           if($Mat->item_id == 500){
               $money = $Mat->mater_need;
               continue;
           }

            $Mat->MatPrice();
            if(in_array($Mat->item_id,$flowers) and $Mat->mater_need < 0){
                $Mat->priceData->price = $this->craft_price;
                $Mat->priceData->autor = 1;
                $Mat->priceData->how = 'Себестоимость (крафт)';
            }
            $mater_need = $Mat->mater_need * $u_amount;
            $tooltip = $Mat->ToolTip($mater_need);


            $Cubik = new Cubik($Mat->item_id,$Mat->icon,$Mat->need_grade,$tooltip,$mater_need);
            $Cubik->print();
        }

        if($money){
            ?><div class="crmoney"><?php echo esyprice($money);?></div><?php
        }

        return true;
    }

    public function matArea(int $u_amount,$Item): bool
    {
        $mats = $this->getMats();
        if(!$mats or !count($mats)){
            echo 'Материалы не найдены';
            return false;
        }

        ?>
        <div class="matarea">
            <div class="matrow">

                <?php
                $dtitle = '';
                if($this->isbest > 1){
                    $dtitle = 'Сбросить';

                }elseif(count($Item->crafts) > 1 and !$this->isbest){
                    $dtitle = 'Предпочитать этот';
                }
                ?>


                <div class="main_itim"
                     id="cr_<?php echo $this->craft_id?>"
                     name="<?php echo $this->result_item_id?>"
                     style="background-image: url('/img/icons/50/<?php echo $Item->icon?>.png')">
                    <div class="grade"
                         data-tooltip="<?php echo $dtitle?>"
                         style="background-image: url(/img/grade/icon_grade<?php echo $this->grade?>.png)">
                        <div class="matneed"><?php echo $this->result_amount*$u_amount?></div>
                    </div>
                </div>

                <?php self::MaCubiki($mats,$u_amount); ?>

            </div>
        </div>
        <?php
        return true;
    }
}
