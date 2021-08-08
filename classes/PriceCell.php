<?php

class PriceCell
{
    public string $date = '00.00.0000';
    public int $timestamp = 0;

    public function __construct(
        public int    $item_id,
        public int    $grade,
        public string $icon,
        public int    $price,
        public string $time = '',
        public bool   $checked = false,
        public bool   $havingChekbox = false,
        public bool   $isPrivate = false,
        public string $tooltip = '',
        public string $item_name = ''
    )
    {
        $this->grade = $grade ?? 1;

        if(!empty($time)){
            $this->timestamp = strtotime($time);
            $this->date = date('d.m.Y',$this->timestamp);

        }

    }

}