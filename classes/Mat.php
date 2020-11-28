<?php


class Mat
{
    public int $mat_id;
    public int $result_id;
    public int $need_grade = 1;
    public int $craft_id;
    public int $mater_need;
    public int $price = 0;

    public function Cubik()
    {
        Cubik($this->mat_id,'',$this->need_grade,'',$this->mater_need);
    }
}