<?php


class Item
{
    public int    $item_id = 0;
    public        $valut_id;
    public        $price_buy;
    public        $price_sale;
    public        $is_trade_npc;
    public string $category;
    public string $item_name;
    public        $description;
    public int    $on_off;
    public int    $personal = 0;
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
    public Price $priceData;
    public        $orSum;
    public Craft  $bestCraft;
    public int   $craftable              = 0;
    public int   $auc_price              = 0;
    public array $crafts                 = [];
    public       $valut_icon             = '';
    public int   $bestCraftId            = 0;
    public bool  $ispack                 = false;
    public int   $craft_price            = 0;
    public array $allMats                = [];
    public array $allTrash               = [];
    public array $craftTree              = [];
    public array $craftResults           = [];
    public array $lost                   = [];
    public bool  $isGoldable             = true;
    public array $potential_crafts       = [];
    public array $potentialMatsAndCrafts = [];
    public bool  $isBuyCraft             = false;
    public ValutInfo $ValutInfo;


    public function byId(int $item_id)
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
        if(!$qwe or !$qwe->rowCount())
            return false;
        $q= $qwe->fetchObject();

        if(!self::byQ($q))
            return false;

        return true;
    }

    public function byQ(object $q) : bool
    {
        //printr($q);
        $this->item_id     = $q->item_id;
        $this->valut_id    = $q->valut_id ?? 500;
        $this->price_buy   = $q->price_buy ?? 0;
        $this->price_sale  = $q->price_sale ?? 0;
        $this->category    = $q->category ?? '';
        $this->item_name   = htmlspecialchars($q->item_name);
        $this->description = $q->description ?? '';
        //$this->description = nl2br(htmlspecialchars($q->description));
        $this->on_off       = $q->on_off ?? 1;
        $this->personal     = $q->personal ?? 0;
        $this->craftable    = $q->craftable;
        $this->ismat        = $q->ismat;
        $this->categ_id     = $q->categ_id;
        $this->categ_pid    = $q->categ_pid;
        $this->slot         = $q->slot;
        $this->lvl          = $q->lvl;
        $this->inst         = $q->inst;
        $this->basic_grade  = $q->basic_grade ?? 1;
        $this->forup_grade  = $q->forup_grade;
        $this->icon         = $q->icon;
        $this->md5_icon     = $q->md5_icon;
        $this->valut_name   = $q->valut_name ?? '';
        $this->sgr_id       = $q->sgr_id ?? 0;
        $this->is_trade_npc = $q->is_trade_npc;
        $this->ispack       = self::isPack();
        $this->isBuyCraft   = $q->isbuy ?? self::isBuyCraft();

        self::isGoldable();

        return true;
    }

    public function reConstruct($Item)
    {
        foreach (get_object_vars($Item) as $k => $v){
            $this->$k = $v;
        }
    }

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
        if(!$qwe or !$qwe->rowCount())
            return [];

        foreach ($qwe as $q)
        {
            $crafts[] = $q['craft_id'];
        }
        $this->crafts = $crafts;
        return $crafts;
    }

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
        if(!$qwe or $qwe->rowCount() == 0)
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
        if(!$qwe or !$qwe->rowCount())
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
        if(!$qwe or !$qwe->rowCount())
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
        if(!$qwe or !$qwe->rowCount())
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

    private function clearBuff()
    {
        global $User;
        if(!isset($User->id)){
            die('user');
        }
        qwe("DELETE FROM craft_buffer WHERE `user_id` = '$User->id'");
        qwe("DELETE FROM craft_buffer2 WHERE `user_id` = '$User->id'");
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
        if(!$qwe or !$qwe->rowCount())
            return [];

        $arr = [];
        foreach($qwe as $q)
        {
            $arr[$q['result_item_id']][] = $q['craft_id'];
        }

        return $arr;
    }

    function RecountBestCraft(bool|int $single = false, bool|int $lostIgnore = false)
    {
        global $lost, $User;
        if($single){
            require_once $_SERVER['DOCUMENT_ROOT'] . '/../functions/funct-obhod2.php';
            self::clearBuff();
        }
        $craftarr = [];

        $this->potentialMatsAndCrafts = self::CraftsByDeep();


        if(!isset($lost))
            $lost = [];


        if(!count($this->potentialMatsAndCrafts)) {
            return false;
        }


        $craftarr = self::CraftsBuffering();

        if(count($lost)){
            if(!in_array($_SERVER['SCRIPT_NAME'],[
                    '/hendlers/packs_list.php',
                    '/hendlers/isbuysets.php',
                    '/packres.php',
                    '/hendlers/packpost/packpostinfo.php',
                    /*'/hendlers/packpost/packpostmats.php',*/
                    '/hendlers/packpost/packobj.php',
                    '/test.php'
                ]) and !$lostIgnore) {

                $lost = MissedList($lost);
                $User->clearUCraftCache();
                exit();
            }else
            {
                $lost = MissedList($lost);
                return false;
            }
        }



        if(!count($craftarr)) {
            return false;
        }

        foreach ($craftarr as $craftId => $itemId)
        {
            $Item = new Item();
            $Item->byId($itemId);
            $Item->orSum = $Item->orTotal(0,1,$craftId);

            if($Item->orSum)
                qwe("
                UPDATE `user_crafts` 
                SET `labor_total` = '$Item->orSum' 
                WHERE `user_id` = '$User->id' 
                AND `craft_id`='$craftId'
            ");

        }

        if($single){
            self::clearBuff();
        }

        return true;
    }

    public static function initLost(array $losted)
    {
        $losted = array_unique($losted);
        $ll = [];
        foreach ($losted as $l){
            $Litem = new Item();
            $Litem->byId($l);
            $ll[] = $Litem;
        }
        return $ll;
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
        if(!$qwe or !$qwe->rowCount())
            return false;

        $q                 = $qwe->fetchObject();
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
        if(!$qwe or !$qwe->rowCount())
            return 0;

        $q= $qwe->fetchObject();
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

    public function insertAllMats(int $craft_id) : bool
    {
        if(!count($this->allMats)){
            return false;
        }

        global $User;

        $allMats = json_encode($this->allMats);
        $qwe = qwe("
            UPDATE user_crafts 
            SET allMats = :allMats
            WHERE user_id = :user_id
                AND craft_id = :craft_id
            ", ['allMats' => $allMats, 'user_id' => $User->id, 'craft_id' => $craft_id]
        );
        if($qwe)
            return true;

        return false;

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

            if($mat->craftable and $mat->item_group != 23 and !$mat->isBuyCraft){

                $arr = $mat->getAllMats($arr,$mat->mater_need*$need/$Craft->result_amount);
                continue;
            }


            if(array_key_exists($mat->item_id,$arr))
                $arr[$mat->item_id] += $mat->mater_need*$need/$Craft->result_amount;
            else
                $arr[$mat->item_id] = $mat->mater_need*$need/$Craft->result_amount;

        }
        $this->allMats = $arr;
        self::insertAllMats($craftId);
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
        if(!$qwe or !$qwe->rowCount())
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
        if(!$qwe or !$qwe->rowCount())
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

    public function valutIcon() : string
    {
        if(!empty($this->valut_icon))
            return $this->valut_icon;

        $qwe = qwe("
            SELECT `icon` FROM `items`
            WHERE `item_id` = '$this->valut_id'
            ");
        if(!$qwe or !$qwe->rowCount())
            return '';
        $q= $qwe->fetchObject();

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
            if($Mat->craftable and !$Mat->isBuyCraft){

                echo $Mat->getBestCraft();
                //printr([$Mat->name,$Mat->is_buyable]);
                $arr = $Mat->craftTree($arr,$i);
            }
               // printr([$Mat->name,$Mat->is_buyable]);

        }

        return $arr;
    }

    public function getCraftResults() : array
    {
        if(count($this->craftResults)){
            return $this->craftResults;
        }

        $qwe = qwe("
        SELECT craft_materials.result_item_id FROM craft_materials  
        INNER JOIN items ON items.item_id = craft_materials.result_item_id
        AND items.on_off
        AND craft_materials.item_id = :item_id
        INNER JOIN crafts c on craft_materials.craft_id = c.craft_id
        AND c.on_off
        GROUP BY c.result_item_id
        ",
        ['item_id' => $this->item_id]
        );
        if(!$qwe or !$qwe->rowCount()){
            return [];
        }
        $this->craftResults = $qwe->fetchAll(PDO::FETCH_COLUMN,0);
        return $this->craftResults;
    }

    public function isGoldable() : bool
    {
        $this->isGoldable = true;
        if(in_array($this->categ_id,[133,171,122])){
            $this->isGoldable = false;
            return $this->isGoldable;
        }

        if($this->is_trade_npc){

            if($this->valut_id == 500){
                $this->isGoldable = false;
                return $this->isGoldable;
            }

            if($this->personal){
                $this->isGoldable = false;
                return $this->isGoldable;
            }



        }

        if($this->personal and $this->craftable){
            $this->isGoldable = false;
            return $this->isGoldable;
        }



        return $this->isGoldable;
    }

    public function initPrice()
    {
        $this->priceData = new Price($this->item_id);
        $this->priceData->initData();
    }

    public function isBuyCraft() : bool
    {

        if(!$this->craftable || $this->personal)
            return false;

        global $User;

        $qwe = qwe("
            SELECT * FROM user_buys 
            WHERE item_id = :item_id 
              AND user_id = :user_id
        ", ['item_id' => $this->item_id, 'user_id' => $User->id]);
        if($qwe && $qwe->rowCount())
            return true;

        return false;
    }

    public function setAsBuy(bool $multi = false) : string
    {
        global $User;

        if(!$this->craftable){
            return '!craftable';
        }

        if($this->personal){
            return 'personal';
        }

        self::initPrice();

        if(!$this->priceData->price){
            return '!price';
        }

        if($this->priceData->autor != $User->id){
            if(!$this->priceData->insert($this->priceData->price)){
                return 'priceInsertErr';
            }
        }


        $qwe = qwe("
            DELETE FROM user_crafts
            WHERE user_id = :user_id 
            AND isbest < 2
        ",['user_id' => $User->id]);
        if(!$qwe)
            return 'delErr';


        $qwe = qwe("
            REPLACE INTO user_buys
            (user_id, item_id)
            values 
            (:user_id, :item_id)
        ",['user_id' => $User->id, 'item_id' => $this->item_id]);
        if(!$qwe)
            return 'regErr';

        if(!$multi)
            $User->clearUCraftCache();


        return 'ok';

    }

    public function unsetAsBuy() : bool
    {

        global $User;

        $qwe = qwe("DELETE FROM user_buys
            WHERE user_id = :user_id
            AND item_id = :item_id
            ",['user_id' => $User->id, 'item_id' => $this->item_id]
        );
        if(!$qwe){
            return false;
        }

        self::delCountedCrafts();

        return $User->clearUCraftCache();

    }

    public function setUserBestCraft(int $craft_id)
    {
        global $User;

        self::delCountedCrafts();


        qwe("REPLACE INTO `user_crafts`
            (user_id, craft_id, item_id, isbest, updated) 
            VALUES 
            (:user_id, :craft_id, :item_id, :isbest, now())
            ", ['user_id' => $User->id, 'craft_id' => $craft_id, 'item_id' => $this->item_id,'isbest'=> 2]
        );


        $User->clearUCraftCache();

    }

    public function unsetBestCraft() : bool
    {
        global $User;
        self::delCountedCrafts();
        return $User->clearUCraftCache();
    }

    private function delCountedCrafts() : void
    {
        $this->isCounted = false;
        global $User;
        $qwe = qwe("
            DELETE FROM user_crafts 
            WHERE  item_id = :item_id 
            AND user_id = :user_id",
            ['user_id' => $User->id, 'item_id' => $this->item_id]
        );

    }

    public static function isCurrency(int $item_id) : bool
    {
        return in_array($item_id,[2,3,4,5,6,23633]);
    }

    private function isValutable() : bool
    {
        return ($this->is_trade_npc && $this->valut_id !=500) ||  self::isCurrency($this->item_id);
    }

    public function initValutInfo() : bool
    {
        if(!self::isValutable()){
            return false;
        }

        $vid = $this->item_id;
        $icon = $this->icon;

        if(!self::isCurrency($this->item_id)){
            $vid = $this->valut_id;
            $icon = self::valutIcon();
        }


        $this->ValutInfo = new ValutInfo(
                valut_id: $vid,
                icon: $icon
        );

        return true;
    }

    public function isPrivate() : bool
    {
        return match (true){
            $this->item_id == 500 => false,
            self::isCurrency($this->item_id) => true,
            $this->is_trade_npc => false,
            $this->personal != 1 => false,
            $this->craftable == 1 => false,
            $this->ismat != 1 => false,
            default => false
        };
    }

    public function isPack()
    {
        return in_array($this->categ_id, [133, 171]);
    }

}