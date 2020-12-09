<?php


class Mat
{
    public int $id;
    public string $name = '';
    public int $result_id = 0;
    public int $need_grade = 1;
    public int $craft_id = 0;
    public float $mater_need = 0;
    public int $price = 0;
    public int $spm2 = 0;
    public bool $is_buyable = false;

    public function Cubik()
    {
        Cubik($this->id,'',$this->need_grade,'',$this->mater_need);
    }

    public function InitForCraft($q,$user_id)
    {
        $q = (object) $q;
        if($q->mater_need == 0)
            return false;

        $this->id = $q->id;
        $this->mater_need = $q->mater_need;
        $this->need_grade = $q->mat_grade;
        $this->name = $q->item_name ?? '';
        $this->spm2 = intval($q->spm2);

        if($q->isbest == 3)
        {
            $this->is_buyable = true;
            $this->price = UserMatPrice($this->id,$user_id,1);
            return true;
        }

        $craftignore = ($this->mater_need < 0);
        if($craftignore or (!$q->craftable))
        {
            if($craftignore)
            {
                global $trash;
                $trash = 1;
            }

            $this->price = UserMatPrice($this->id,$user_id,$craftignore);
            if($this->price)
                return true;
        }

        if($q->buffer_price)
        {
            $this->price = $q->buffer_price;
            //echo '<p>откопал в промежуточных: '.$q->item_name.' '.$this->price.'</p>';
            return true;
        }

        if(!$q->craftable)
        {
            global $lost;
            $lost[] = $this->id;
        }


        return false;
    }
}