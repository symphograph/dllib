<?php


class Mat extends Item
{
    public int $result_id = 0;
    public int $need_grade = 1;
    public int $craftId = 0;
    public float $mater_need = 0;
    public int $price = 0;
    public int $spm2 = 0;
    public bool $is_buyable = false;

    public function Cubik(){
        Cubik($this->id,'',$this->need_grade,'',$this->mater_need);
    }

    public function byRcost($q)
    {
        global $trash, $lost, $User;
        $q = (object) $q;
        if($q->mater_need == 0)
            return false;

        $this->id = $q->id;
        $this->mater_need = $q->mater_need;
        $this->need_grade = $q->mat_grade;
        $this->name = $q->item_name ?? '';
        $this->valut_id = $q->valut_id ?? 500;
        $this->spm2 = intval($q->spm2);
        $this->is_trade_npc = $q->is_trade_npc;
        $this->price_buy = $q->price_buy;
        $this->craftable = $q->craftable;


        if($this->mater_need < 0) {
            $trash = 1;
            if(self::MatPrice())
                return true;

            $lost[] = $this->id;
            return false;
        }

        if($q->isbest == 3) {
            $Price = new Price();
            $Price->byMode($this->id);
            if($Price->price){
                $this->price = $Price->price;
                return true;
            }

            $lost[] = $this->id;
            return false;
        }


        if(!$q->craftable) {

            if(self::MatPrice())
                return true;

            $lost[] = $this->id;
            return false;
        }

        if($q->buffer_price) {
            $this->price = $q->buffer_price;
            //echo '<p>откопал в промежуточных: '.$q->item_name.' '.$this->price.'</p>';
            return true;
        }

        //$lost[] = $this->id;
        return false;
    }

    public function MatPrice() : bool
    {

        if($this->id == 500){
            $this->price = 1;
            return true;
        }

        $Price = new Price();

        if($this->is_trade_npc) {

            if($this->valut_id == 500){

                $this->price = $this->price_buy;
                return true;
            }

            $Price->byMode($this->id);
            if($Price->price){

                $this->price = $Price->price;
                return true;
            }

            $Price->byMode($this->valut_id);
            if($Price->price){

                $this->price = $Price->price * $this->price_buy;
                return true;
            }
            return false;
        }

        if($this->craftable and $this->mater_need > 0) {

            $Price->byCraft($this->id);
            if($Price->price) {
                $this->price = $Price->price;
                return true;
            }
        }

        $Price->byMode($this->id);
        if($Price->price) {
            $this->price = $Price->price;
            return true;
        }

        return false;
    }

    public function byCraft()
    {
        self::getFromDB($this->id);
    }
}