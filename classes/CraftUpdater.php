<?php

/**
 * @property bool $valid;
 */
class CraftUpdater extends Craft
{
    private bool  $valid = false;
    private array $args  = [];

    public function __construct(
        int $craft_id,
        int $result_amount = 1,
        int $result_item_id = 0,
        int $prof_id = 27,
        int $prof_need = 0,
        int $dood_id = 0,
        string $rec_name = '',
        int $labor_need = 0,
        int $mins = 0
    )
    {
        parent::__construct();
        if (!$craft_id)
            return false;

        $this->args = get_defined_vars();
        foreach ($this->args as $ak => $av) {
            $this->$ak = $av;
        }

        $this->rec_name = OnlyText($rec_name);
        $this->profession = AnyById($prof_id, 'profs', 'profession')[$prof_id] ?? '';
        $this->dood_name = AnyById($dood_id, 'doods', 'dood_name')[$dood_id] ?? '';
        $this->valid = true;


        return true;
    }

    public function __get(string $name)
    {
        return $this->$name;
    }

    public function upIndb(): bool
    {
        if (!$this->valid)
            return false;

        $str = sqlUpdateArgsToString($this->args);
        $sql = "UPDATE `crafts` SET  $str  WHERE `craft_id` = '$this->craft_id'";

        $qwe = qwe($sql);
        if (!$qwe)
            return false;

        return true;
    }

    public function recountSPM()
    {
        $SPM_array = SPM_array();
        if (!$SPM_array or !count($SPM_array))
            return false;

        foreach ($SPM_array as $spmcraft_id => $spm) {
            qwe("
            UPDATE crafts
            SET spm = '$spm'
            WHERE craft_id = '$spmcraft_id'
            ");
        }
        return true;
    }

    /**
     *[item_id]=>mater_need
     */
    public function upMatNeeds(array $needs): bool
    {

        if (!count($needs))
            return false;

        foreach ($needs as $mat_id => $need) {
            $mat_id = intval($mat_id);
            $need = floatval($need);

            qwe("
            UPDATE `craft_materials` 
            SET `mater_need`='$need'
            WHERE `item_id`='$mat_id' 
            AND `craft_id`='$this->craft_id'
            ");

        }
        return true;
    }

    public function delSomeMats(array $mats): bool
    {
        if (!count($mats)) {
            return false;
        }
        $dels = implode(', ', $mats);

        qwe("
            DELETE FROM `craft_materials` 
            WHERE `craft_id`='$this->craft_id' 
            AND `item_id` in ($dels)
            ");


        qwe("
            UPDATE `items` 
            SET `ismat` = 0 
            WHERE `item_id` in ($dels) 
            AND `item_id` 
            NOT in (SELECT `item_id` FROM `craft_materials` where `item_id` in ($dels))
            ");

        return true;
    }

    public function addMats(array $mats) : bool
    {
        foreach ($mats as $item_id => $newneed) {

            $item_id = intval($item_id);
            $newneed = floatval($newneed);
            if (!$item_id or !$newneed)
                return false;

            $qwe = qwe("INSERT INTO `craft_materials` 
            (`craft_id`, `item_id`, `result_item_id`, `mater_need`)
            VALUES 
            ('$this->craft_id','$item_id', '$this->result_item_id', '$newneed')");
            if(!$qwe)
                continue;

            qwe("UPDATE `items` SET `ismat`= 1
                WHERE `item_id` = '$item_id'");

        }
        return true;
    }
}