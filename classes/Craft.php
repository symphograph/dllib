<?php


class Craft
{
    public int $id;
    public $rec_name;
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
    public $prof_id;
    public $grade;
    public $mins;
    public $spm;
    public array $mats = [];
    public int $isbest = 0;
    public int $orcost = 250;

    //ОР с учетом прокачки профы у юзера
    public int $labor_need2 = 0;

    //ОР на один рецепт с учетом прокачки профы у юзера
    public float $labor_single = 0;

    //ОР на всю цепочку крафта
    public float $labor_total = 0;

    public int $spmu = 0;
    public int $craft_price = 0;

    public function __construct(int $craft_id)
    {
        $craft_id = intval($craft_id);
        $qwe = qwe("SELECT * FROM crafts WHERE on_off AND craft_id = '$craft_id'");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);


        $this->id = $q->craft_id;
        $this->rec_name = $q->rec_name;
        $this->dood_id = $q->dood_id;
        $this->dood_name = $q->dood_name;
        $this->result_item_id = $q->result_item_id;
        $this->result_item_name = $q->result_item_name;
        $this->labor_need = $q->labor_need;
        $this->profession = $q->profession;
        $this->prof_need = $q->prof_need;
        $this->result_amount = $q->result_amount;

        if($q->dood_id == 9131)
            $this->result_amount = $q->result_amount * 1.1;

        $this->on_off = $q->on_off;
        $this->isbottom = $q->isbottom;
        $this->dood_group = $q->dood_group;
        $this->deep = $q->deep;
        $this->my_craft = $q->my_craft;
        $this->craft_time = $q->craft_time;
        $this->prof_id = $q->prof_id;
        $this->grade = $q->grade;
        $this->mins = $q->mins;
        $this->spm = $q->spm;
        $this->mats = self::getMats();
        return true;
    }

    public function getMats() : array
    {
        $mats = [];
        $qwe = qwe("
        SELECT * FROM craft_materials 
        WHERE craft_id = '$this->id'
        ");
        foreach ($qwe as $q)
        {
            $q = (object) $q;
            $mat = new Mat();
            $mat->id = $q->item_id;
            $mat->mater_need = $q->mater_need;
            $mat->need_grade = $q->mat_grade;
            $mats[$mat->id] = $mat;
        }
        return $mats;
    }

    public function InitForUser(int $user_id)
    {
        $Prof = new Prof();
        $Prof->InitForUser($this->prof_id, $user_id);

        $labor_need2 = $this->labor_need*((100 - $Prof->save_or) * 0.01);
        $labor_need2 = round($labor_need2,0);

        $labor_single = $labor_need2 / $this->result_amount;
        $labor_single = round($labor_single,2);

        $this->labor_need2 = $labor_need2;
        $this->labor_single = $labor_single;

        $this->orcost = PriceMode(2)['auc_price'] ?? 250;
    }

    public function setCountedData(int $user_id)
    {
        $qwe = qwe("SELECT * FROM user_crafts 
        WHERE user_id = '$user_id'
        AND craft_id = '$this->id'
        ") ;
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->isbest = $q->isbest;
        $this->spmu = $q->spmu;
        $this->craft_price = $q->craft_price;
        $this->labor_total = $q->labor_total;


        return true;
    }

    public function rescost($user_id) : array
    {

        $groupcraft = self::GroupCraft($this);
        if($groupcraft)
        {
            $this->craft_price = round($groupcraft / $this->result_amount);
            return [$this->craft_price,0];
        }



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
        AND `craft_materials`.`craft_id` = '$this->id'
        AND `items`.`ismat`
        AND `items`.`on_off`
        LEFT JOIN `user_crafts` 
        ON `items`.`item_id` = `user_crafts`.`item_id`
        AND `user_crafts`.`user_id` = '$user_id'
        AND `user_crafts`.`isbest` > 0
        LEFT JOIN craft_buffer2 
        ON `craft_materials`.`item_id` = craft_buffer2.item_id
        AND craft_buffer2.user_id = '$user_id'
        ORDER BY `id`");
        $sum = $sumspm = 0;
        foreach($qwe as $q)
        {

            $mat = new Mat;
            $mat->InitForCraft($q,$user_id);
            //printr($mat);
            $spm2 = $mat->spm2;

            $mater_need = $mat->mater_need;
            if($mater_need == 0) continue;


            //echo $q['item_name'].'<br>';
            //var_dump($q['buffer_price']);
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

    public function GroupCraft(object $Craft)
    {

        global $lost, $user_id;
        //extract($arritog);
        $qwe = qwe("
        SELECT `item_name`, `amount`, sum(`amount`) as `sum`
        FROM `craft_groups` 
        WHERE `group_id` = 
        (SELECT `group_id` FROM `craft_groups` WHERE `craft_id` = '$Craft->id')
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $gcr = mysqli_fetch_assoc($qwe);
        $am_sum = $gcr['sum'];
        if(!$am_sum ) return false;

        $itog = $Craft->result_amount;
        $cr_part = $itog/$am_sum;
        $itog = $itog/$cr_part;


        $qwe = qwe("
        SELECT 
        `craft_materials`.`item_id` as mater,
        `craft_materials`.`mater_need`,
        `items`.`craftable`,
        `items`.`personal`,
        `user_crafts`.`isbest`,
        `user_crafts`.`craft_price`
        FROM `craft_materials`
        INNER JOIN `items` 
        ON `craft_materials`.`item_id` = `items`.`item_id`
        AND `craft_materials`.`craft_id` = '$Craft->id' 
        AND `craft_materials`.`mater_need` > 0
        LEFT JOIN `user_crafts` 
        ON `items`.`item_id` = `user_crafts`.`item_id`
        AND `user_crafts`.`user_id` = '$user_id'
        AND `user_crafts`.`isbest` > 0
        ");
        $sum = 0; $price = 0;
        foreach($qwe as $gcr)
        {
            //extract($gcr);
            $gcr = (object) $gcr;

            if($gcr->isbest)
            {
                //echo $mater.' '.$mater_need.'ыапаывапыв</p>';
                if($gcr->isbest == 3)
                    $price = UserMatPrice($gcr->mater, $user_id,1);
                else
                    $price = $gcr->craft_price;


                $matsum = $gcr->mater_need * $price;
                $sum = $sum + $matsum;
                continue;
            }

            if(!$gcr->craftable)
            {

                $user_aucprice = UserMatPrice($gcr->mater,$user_id,1);
                if($user_aucprice)
                {
                    $price = $user_aucprice;
                    $matsum = $gcr->mater_need * $price;
                    $sum = $sum + $matsum;
                    continue;
                }
            }


            if(!$price and !$gcr->craftable)
                $lost[] = $gcr->mater;


            $matsum = $gcr->mater_need * $price;
            $sum = $sum + $matsum;

        }
        //echo '<p>Итм-ов: '.$or.' по '.$orcost.'</p>';

        $total = $sum + $Craft->labor_need2 * $Craft->orcost;

        //echo $total.'<br>';
        return $total;
    }
}
