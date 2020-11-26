<?php


class Item
{
    public int $item_id;
    public $price_type;
    public $valut_id;
    public $price_sale;
    public $category;
    public $item_name;
    public $description;
    public $on_off;
    public $personal;
    public $craftable;
    public $ismat;
    public $categ_id;
    public $categ_pid;
    public $slot;
    public $lvl;
    public $inst;
    public $basic_grade;
    public $forup_grade;
    public $icon;
    public $md5_icon;

    public function __construct(int $item_id)
    {
        $qwe = qwe("
        SELECT * FROM `items` 
        WHERE `item_id` = '$item_id'
        ");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);

        $this->item_id = $item_id;
        $this->price_type = $q->price_type;
        $this->valut_id = $q->valut_id;
        $this->price_sale = $q->price_sale;
        $this->category = $q->category;
        $this->item_name = $q->item_name;
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
        WHERE `result_item_id` = '$this->item_id'
        AND `on_off`
        ");
        if(!$qwe or !$qwe->num_rows)
            return [];

        foreach ($qwe as $q)
        {
            $crafts[] = $q['craft_id'];
        }
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
            $craftable = $q['craftable'];
            $arr[] = $id;
            if($craftable)
                $arr = self::AllPotentialMats($id, $arr,$i);
        }
        return $arr;
    }

    function AllPotentialCrafts()
    {
        $items = self::AllPotentialMats($this->item_id);
        if(!count($items))
            return [];
        $str = implode(',',$items);

        $qwe = qwe("SELECT craft_id FROM crafts WHERE result_item_id IN ( $str )");
        if(!$qwe or !$qwe->num_rows)
            return [];

        $crafts = [];
        foreach ($qwe as $q)
        {
            $crafts[] = $q['craft_id'];
        }
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

    function getPrice(int $user_id)
    {


    }
}