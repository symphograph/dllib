<?php


class Craft
{
    public $craft_id;
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


    public function __construct(int $craft_id)
    {
        $craft_id = intval($craft_id);
        $qwe = qwe("SELECT * FROM crafts WHERE on_off AND craft_id = $craft_id");
        if(!$qwe or !$qwe->num_rows)
            return false;
        $q = mysqli_fetch_object($qwe);


        $this->craft_id = $q->craft_id;
        $this->rec_name = $q->rec_name;
        $this->dood_id = $q->dood_id;
        $this->dood_name = $q->ood_name;
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

        return true;
    }


}
