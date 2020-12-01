<?php


class Item
{
    public int $id;
    public $valut_id;
    public $price_buy;
    public $price_sale;
    public $is_trade_npc;
    public string $category;
    public $name;
    public $description;
    public int $on_off;
    public int $personal;
    public int $craftable;
    public int $ismat;
    public $categ_id;
    public $categ_pid;
    public $slot;
    public $lvl;
    public $inst;
    public $basic_grade;
    public $forup_grade;
    public $icon;
    public $md5_icon;
    public $valut_name;
    public $sgr_id;
    public int $auc_price = 0;
    public array $crafts = [];
    public array $potential_crafts = [];
    public bool $ispack = false;


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

        $this->id = $item_id;
        $this->valut_id = $q->valut_id;
        $this->price_buy = $q->price_buy;
        $this->price_sale = $q->price_sale;
        $this->category = $q->category;
        $this->name = htmlentities($q->item_name);
        $this->description = $q->description;
        $this->on_off = $q->on_off;
        $this->personal = $q->personal;
        $this->craftable = $q->craftable;
        $this->ismat = $q->ismat;
        $this->categ_id = $q->categ_id;
        $this->categ_pid = $q->categ_pid;
        $this->slot = $q->slot;
        $this->lvl = $q->lvl;
        $this->inst = $q->inst;
        $this->basic_grade = $q->basic_grade;
        $this->forup_grade = $q->forup_grade;
        $this->icon = $q->icon;
        $this->md5_icon = $q->md5_icon;
        $this->valut_name = $q->valut_name;
        $this->sgr_id = $q->sgr_id;
        $this->is_trade_npc = $q->is_trade_npc;
        $this->ispack = ($this->categ_id == 133);

        return true;
    }

    /**
     * @return array
     * Возвращает рецепты
     */
    public function getCrafts() : array
    {
        $crafts = [];
        $qwe = qwe("
        SELECT `craft_id` FROM `crafts` 
        WHERE `result_item_id` = '$this->id'
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
    function AllPotentialMats(int $item_id, array $arr=[], int $i=0)
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

    function AllPotentialCrafts()
    {
        if(!count($this->crafts))
            $crafts = self::getCrafts();
        else
            $crafts = $this->crafts;

        $items = self::AllPotentialMats($this->id);
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
        $mats = self::AllPotentialMats($this->id);
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
    function AllPotentialResults(int $item_id, array $arr=[], int $i=0)
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

    public function CraftsByDeep()
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
}