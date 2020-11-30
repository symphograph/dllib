<?php


class Craft
{
    public $id;
    public $rec_name;
    public $dood_id;
    public $dood_name;
    public $result_item_id;
    public $result_item_name;
    public $labor_need;
    public $profession;
    public $prof_need;
    public $result_amount;
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
        $Prof->InitForUser($this->id, $user_id);

        $labor_need2 = $this->labor_need*((100 - $Prof->save_or) * 0.01);
        $labor_need2 = round($labor_need2,0);

        $labor_single = $labor_need2 / $this->result_amount;
        $labor_single = round($labor_single,2);

        $this->labor_need2 = $labor_need2;
        $this->labor_single = $labor_single;

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

}
