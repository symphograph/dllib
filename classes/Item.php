<?php


class Item
{
    public int    $item_id;
    public        $valut_id;
    public        $valut_icon             = '';
    public        $price_buy;
    public        $price_sale;
    public        $is_trade_npc;
    public string $category;
    public string $item_name;
    public        $description;
    public int    $on_off;
    public int    $personal;
    public int    $craftable;
    public int    $ismat;
    public int    $item_group;
    public        $categ_id;
    public        $categ_pid;
    public        $slot;
    public        $lvl;
    public        $inst;
    public        $basic_grade;
    public        $forup_grade;
    public        $icon;
    public        $md5_icon;
    public        $valut_name;
    public        $sgr_id;
    public int    $auc_price              = 0;
    public array  $crafts                 = [];
    public array  $potential_crafts       = [];
    public Craft  $bestCraft;
    public int    $bestCraftId            = 0;
    public bool   $ispack                 = false;
    public int    $craft_price            = 0;
    public array  $potentialMatsAndCrafts = [];
    public        $orSum;
    public array  $allMats                = [];
    public array  $allTrash               = [];
    public object $priceData;
    public array  $craftTree              = [];


    public function getFromDB(int $item_id)
    {
        $qwe = qwe("
        SELECT
        items.*,
        item_categories.item_group,
        item_categories.`name` as category,
        valutas.valut_name,
        `item_subgroups`.`sgr_id`
        FROM
        items
        INNER JOIN item_categories ON items.categ_id = `item_categories`.`id`
        AND `items`.`on_off` = 1 AND `items`.`item_id` = '$item_id'
        LEFT JOIN `valutas` ON `valutas`.`valut_id` = `items`.`valut_id`
        LEFT JOIN `item_groups` ON `item_groups`.id = item_categories.item_group
        LEFT JOIN `item_subgroups` ON `item_subgroups`.sgr_id = `item_groups`.sgr_id
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        if(!self::byQ($q))
            return false;

        return true;
    }

    public function byQ(object $q) : bool
    {
        $this->item_id  = $q->item_id;
        $this->valut_id = $q->valut_id ?? 500;
        $this->price_buy = $q->price_buy ?? 0;
        $this->price_sale = $q->price_sale ?? 0;
        $this->category = $q->category ?? '';
        $this->item_name = htmlspecialchars($q->item_name);
        $this->description = $q->description ?? '';
        //$this->description = nl2br(htmlspecialchars($q->description));
        $this->on_off = $q->on_off ?? 1;
        $this->personal = $q->personal;
        $this->craftable = $q->craftable;
        $this->ismat = $q->ismat;
        $this->categ_id = $q->categ_id;
        $this->categ_pid = $q->categ_pid;
        $this->slot = $q->slot;
        $this->lvl = $q->lvl;
        $this->inst = $q->inst;
        $this->basic_grade = $q->basic_grade ?? 1;
        $this->forup_grade = $q->forup_grade;
        $this->icon = $q->icon;
        $this->md5_icon = $q->md5_icon;
        $this->valut_name = $q->valut_name ?? '';
        $this->sgr_id = $q->sgr_id ?? 0;
        $this->is_trade_npc = $q->is_trade_npc;
        $this->ispack = (in_array($this->categ_id,[133,171]));

        return true;
    }

    public function reConstruct($Item)
    {
        foreach (get_object_vars($Item) as $k => $v){
            $this->$k = $v;
        }
    }

    /**
     * @return array
     * Возвращает рецепты
     */
    public function getCrafts() : array
    {
        if(count($this->crafts))
            return $this->crafts;


        $crafts = [];
        $qwe = qwe("
        SELECT `craft_id` FROM `crafts` 
        WHERE `result_item_id` = '$this->item_id'
        AND `on_off`
        ");
        if(!$qwe or !$qwe->num_rows)
            return [];

        foreach ($qwe as $q)
        {
            $crafts[] = $q['craft_id'];
        }
        $this->crafts = $crafts;
        return $crafts;
    }

    /**
     * @param int $item_id
     * @param array $arr
     * @param int $i
     * @return array
     * Возможные материалы (включая дочерние)
     */
    public function AllPotentialMats(int $item_id, array $arr=[], int $i=0)
    {
        $i = intval($i);
        $i++;
        $qwe = qwe("
        SELECT 
        crafts.craft_id,
        items.item_id,
        items.item_name,
        items.craftable
        FROM craft_materials
        inner join items on craft_materials.item_id = items.item_id
        AND craft_materials.result_item_id = '$item_id'
        AND items.on_off
        AND craft_materials.mater_need > 0
        inner join crafts on crafts.craft_id = craft_materials.craft_id
        AND crafts.on_off
        ");
        if(!$qwe or $qwe->num_rows == 0)
            return $arr;

        foreach ($qwe as $q)
        {

            $id = $q['item_id'];
            if(in_array($id,$arr))
                continue;

            $arr[] = $id;
            if($q['craftable'])
                $arr = self::AllPotentialMats($id, $arr,$i);
        }
        return $arr;
    }

    public function AllPotentialCrafts()
    {
        if(!count($this->crafts))
            $crafts = self::getCrafts();
        else
            $crafts = $this->crafts;

        $items = self::AllPotentialMats($this->item_id);
        if(!count($items))
            return $crafts;

        $str = implode(',',$items);

        $qwe = qwe("
        SELECT craft_id FROM crafts 
        WHERE result_item_id IN ( $str )
        ");
        if(!$qwe or !$qwe->num_rows)
            return $crafts;

        foreach ($qwe as $q)
        {
            $crafts[] = $q['craft_id'];
        }
        sort($crafts);
        $this->potential_crafts = $crafts;
        return $crafts;
    }

    /**
     * @return array
     * Первичные некрафтабельные материалы
     */
    public function getPrimaryMats()
    {
        $mats = self::AllPotentialMats($this->item_id);
        if(!count($mats))
            return [];

        $mats = implode(',',$mats);
        $qwe = qwe("SELECT * FROM items WHERE item_id IN ($mats) AND on_off and !craftable");
        if(!$qwe or !$qwe->num_rows)
            return [];

        $arr = [];
        foreach ($qwe as $q)
        {
            $arr[] = $q['item_id'];
        }
        return $arr;
    }

    /**
     * @return array
     * Первичные некрафтабельные материалы
     */
    public function AllPotentialResults(int $item_id, array $arr=[], int $i=0)
    {
        $i = intval($i);
        $i++;

        $qwe = qwe("
        SELECT 
        crafts.craft_id,
        items.item_id,
        items.item_name,
        items.ismat
        FROM craft_materials
        inner join items on craft_materials.result_item_id = items.item_id
        AND craft_materials.item_id = '$item_id'
        AND craft_materials.mater_need > 0                        
        AND items.on_off
        inner join crafts on crafts.craft_id = craft_materials.craft_id
        AND crafts.on_off
        AND crafts.craft_id not in (SELECT craft_id FROM craft_groups)
        GROUP BY items.item_id
        ");
        if(!$qwe or !$qwe->num_rows)
            return $arr;

        foreach ($qwe as $q)
        {

            $id = $q['item_id'];
            if(in_array($id,$arr))
                continue;

            $arr[] = $id;

            if($q['ismat'])
                $arr = self::AllPotentialResults($id, $arr,$i);
        }
        return $arr;
    }

    public function CraftsByDeep() : array
    {
        if(!count($this->potential_crafts))
            $this->potential_crafts = self::AllPotentialCrafts();

        $str = implode(',', $this->potential_crafts);
        $qwe = qwe("
            SELECT result_item_id, craft_id from `crafts` 
            WHERE `on_off` 
            AND 
                `craft_id` IN ( $str ) 
            ORDER BY 
                `deep` DESC, `result_item_id`");
        if(!$qwe or !$qwe->num_rows)
            return [];

        $arr = [];
        foreach($qwe as $q)
        {
            $arr[$q['result_item_id']][] = $q['craft_id'];
        }

        return $arr;
    }

    function RecountBestCraft()
    {
        global $lost, $User;


        $this->potentialMatsAndCrafts = self::CraftsByDeep();


        if(!isset($lost))
            $lost = [];


        if(count($this->potentialMatsAndCrafts))
        {
            $craftarr = self::CraftsBuffering();



            if(!in_array($_SERVER['SCRIPT_NAME'],[
                '/hendlers/packs_list.php',
                '/hendlers/isbuysets.php',
                '/packres.php',
                '/hendlers/packpost/packpostmats.php',
                '/hendlers/packpost/packobj.php',
                '/test.php'
            ]))
            {

                if(count($lost)>0)
                {
                    MissedList($lost);
                    //$craftsForClean = implode(',',$craftarr);
                    qwe("DELETE FROM user_crafts WHERE user_id = '$User->id' AND isbest < 2");
                    exit();
                }
            }
        }

        if(count($craftarr)) {

            foreach ($craftarr as $craftId => $itemId)
            {
                $Item = new Item();
                $Item->getFromDB($itemId);
                $Item->orSum = $Item->orTotal(0,1,$craftId);

                if($Item->orSum)
                    qwe("
                    UPDATE `user_crafts` 
                    SET `labor_total` = '$Item->orSum' 
                    WHERE `user_id` = '$User->id' 
                    AND `craft_id`='$craftId'
                ");

            }

        }
    }

    public function CraftsBuffering() : array
    {
        global $User,$complited;
        if(!isset($complited))
            $complited = [];


        $craftarr = [];
        foreach($this->potentialMatsAndCrafts as $item_id => $crafts)
        {

            if(array_key_exists($item_id,$complited))
                continue;
            foreach($crafts as $key => $craft_id)
            {

                $Craft = new Craft($craft_id);
                $Craft->InitForUser();


                $rescost = $Craft->rescost();
                $mycost = $rescost[0];
                $matspm = $rescost[1];
                qwe("
                    REPLACE INTO `craft_buffer` 
                    (user_id, craft_id, craft_price, matspm)
                    VALUES
                    ('$User->id', '$craft_id', '$mycost', '$matspm')
                    ");

                $craftarr[$craft_id] = $item_id;

            }

            ToBuffer2($item_id);

            $complited[$item_id] = 1;
            $forLaborRecount[] = $item_id;
        }

        return  $craftarr;
    }

    public function isCounted()
    {
        if(!$this->craftable)
            return false;

        if($this->craft_price)
            return true;

        global $User;
        $qwe = qwe("
            SELECT * FROM `user_crafts` 
            WHERE user_id = '$User->id'
            AND item_id = '$this->item_id'
            ORDER BY isbest DESC 
            LIMIT 1
            ");
        if(!$qwe or !$qwe->num_rows)
            return false;

        $q                 = mysqli_fetch_object($qwe);
        $this->craft_price = intval($q->craft_price);

        if($this->craft_price)
            return true;

        return false;
    }

    public function getBestCraft() : int
    {
        if($this->bestCraftId)
            return $this->bestCraftId;
        global $User;
        $qwe = qwe("
            SELECT * FROM user_crafts 
            WHERE user_id = '$User->id'
            AND item_id = '$this->item_id'
            ORDER BY isbest DESC 
            LIMIT 1
            ");
        if(!$qwe or !$qwe->num_rows)
            return 0;

        $q = mysqli_fetch_object($qwe);
        $this->bestCraftId = $q->craft_id;
        $this->bestCraft = new Craft($q->craft_id);
        return $this->bestCraftId;
    }

    public function orTotal($orsum,float $need = 1,$craftId = 0)
    {
        if(!$craftId)
            $craftId = self::getBestCraft();

        if(!$craftId)
            return $orsum;

        $Craft = new Craft($craftId);
        $Craft->InitForUser();
        $Craft->getMats();
        $orsum += $Craft->labor_single*$need;
        foreach ($Craft->mats as $mat)
        {
            //$mat->getFromDB($mat->item_id);
            //echo $mat->name.' '.$orsum.'<br>';
            if($mat->craftable and $mat->mater_need > 0)
                $orsum = $mat->orTotal($orsum,$mat->mater_need/$Craft->result_amount);

        }
        return $orsum;
    }

    public function getAllMats($arr = [],float $need = 1,$craftId = 0)
    {
        if(!$craftId)
            $craftId = self::getBestCraft();

        if(!$craftId)
            return $arr;

        $Craft = new Craft($craftId);
        $Craft->InitForUser();
        $Craft->getMats();
        foreach ($Craft->mats as $mat)
        {

            //echo $mat->name.' '.$mat->is_buyable.'<br>';

            if ($mat->mater_need < 0)
                continue;

            if($mat->craftable and $mat->item_group != 23 and !$mat->is_buyable){

                $arr = $mat->getAllMats($arr,$mat->mater_need*$need/$Craft->result_amount);
                continue;
            }


            if(array_key_exists($mat->item_id,$arr))
                $arr[$mat->item_id] += $mat->mater_need*$need/$Craft->result_amount;
            else
                $arr[$mat->item_id] = $mat->mater_need*$need/$Craft->result_amount;

        }
        $this->allMats = $arr;
        return $arr;
    }

    public function getAllTrash($arr = [],float $need = 1,$craftId = 0)
    {
        if(!$craftId)
            $craftId = self::getBestCraft();

        if(!$craftId)
            return $arr;

        $Craft = new Craft($craftId);
        $Craft->InitForUser();
        $Craft->getMats();
        foreach ($Craft->mats as $mat)
        {

            //echo $mat->name.' '.$mat->is_buyable.'<br>';

            if ($mat->mater_need < 0){
                $tr = abs($mat->mater_need)*$need/$Craft->result_amount;
                if(array_key_exists($mat->item_id,$arr))
                    $arr[$mat->item_id] += $tr;
                else
                    $arr[$mat->item_id] = $tr;
                continue;
            }


            if($mat->craftable and $mat->mater_need > 0){

                $arr = $mat->getAllTrash($arr,$mat->mater_need*$need/$Craft->result_amount);
                continue;
            }

        }
        $this->allTrash = $arr;
        return $arr;
    }

    public function allMatsShow(int $u_amount, int $result_amount) : bool
    {
        if (!self::getAllMats())
            return false;

        $matStr = implode(',', array_keys($this->allMats));

        $qwe = qwe("
                SELECT
                items.*,
                item_categories.item_group,
                item_categories.`name` as category,
                valutas.valut_name,
                `item_subgroups`.`sgr_id`
                FROM
                items
                INNER JOIN item_categories ON items.categ_id = `item_categories`.`id`
                AND `items`.`on_off` = 1 AND `items`.`item_id` in ( $matStr )
                LEFT JOIN `valutas` ON `valutas`.`valut_id` = `items`.`valut_id`
                LEFT JOIN `item_groups` ON `item_groups`.id = item_categories.item_group
                LEFT JOIN `item_subgroups` ON `item_subgroups`.sgr_id = `item_groups`.sgr_id
                ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        ?>
        <br>
        <details class="details">
            <summary><b>Все требуемые ресурсы для <?php echo $result_amount*$u_amount?>шт</b></summary>
            <div class="all_res_area">
            <?php
            foreach ($qwe as $q)
            {
                $q = (object) $q;

                $Mat = new Mat;
                $Mat->byQ($q);
                $sum = $this->allMats[$Mat->item_id]*$u_amount*$result_amount;
                $Mat->MatPrice();
                $Cubik = new Cubik($Mat->item_id,$Mat->icon,$Mat->basic_grade,$Mat->ToolTip($sum), round($sum,2));
                $Cubik->print();
            }
            ?>
            </div>
        </details>
        <br><hr><br>
        <?php
        return true;
    }

    public function allTrashShow(int $u_amount, int $result_amount) : bool
    {
        if (!self::getAllTrash())
            return false;

        $matStr = implode(',', array_keys($this->allTrash));

        $qwe = qwe("
                SELECT
                items.*,
                item_categories.item_group,
                item_categories.`name` as category,
                valutas.valut_name,
                `item_subgroups`.`sgr_id`
                FROM
                items
                INNER JOIN item_categories ON items.categ_id = `item_categories`.`id`
                AND `items`.`on_off` = 1 AND `items`.`item_id` in ( $matStr )
                LEFT JOIN `valutas` ON `valutas`.`valut_id` = `items`.`valut_id`
                LEFT JOIN `item_groups` ON `item_groups`.id = item_categories.item_group
                LEFT JOIN `item_subgroups` ON `item_subgroups`.sgr_id = `item_groups`.sgr_id
                ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        ?>
        <br>
        <details class="details">
            <summary><b>Полученные отходы с <?php echo $result_amount*$u_amount?>шт</b></summary>
            <div class="all_res_area">
                <?php
                foreach ($qwe as $q)
                {
                    $q = (object) $q;

                    $Mat = new Mat;
                    $Mat->byQ($q);
                    $sum = $this->allTrash[$Mat->item_id]*$u_amount*$result_amount;
                    $Mat->MatPrice();

                    $Cubik = new Cubik($Mat->item_id,$Mat->icon,$Mat->basic_grade,$Mat->ToolTip($sum), round($sum,2));
                    $Cubik->print();
                }
                ?>
            </div>
        </details>
        <br><hr><br>
        <?php
        return true;
    }

    public function ValutIcon() : string
    {
        if(!empty($this->valut_icon))
            return $this->valut_icon;

        $qwe = qwe("
            SELECT `icon` FROM `items`
            WHERE `item_id` = '$this->valut_id'
            ");
        if(!$qwe or !$qwe->num_rows)
            return '';
        $q = mysqli_fetch_object($qwe);

        $this->valut_icon = $q->icon;
        return $q->icon;
    }

    public function craftTree($arr=[],$i = 0) : array
    {
        $i++;

        if(!$this->craftable)
            return $arr;
        $craftId = self::getBestCraft();
        if(!$craftId)
            return $arr;
        $Craft = new Craft($craftId);
        $Craft->getMats();
        foreach ($Craft->mats as $mat){
            $Mat = new Mat();
            $Mat->reConstruct($mat);
            if($Mat->mater_need < 0)
                continue;

            $arr[] = ['deep'=>$i, $this->item_id, $Mat->item_id, $Mat->item_name];
            if($Mat->craftable and !$Mat->is_buyable){

                echo $Mat->getBestCraft();
                //printr([$Mat->name,$Mat->is_buyable]);
                $arr = $Mat->craftTree($arr,$i);
            }
               // printr([$Mat->name,$Mat->is_buyable]);

        }

        return $arr;
    }
}