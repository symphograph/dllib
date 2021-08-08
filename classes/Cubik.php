<?php


class Cubik
{
    public function __construct(

            public int $id,
            public string $icon = '',
            public  $grade = 1,
            public $tooltip = '',
            public $value = ''
    )
    {

        $grade = intval($grade);
        $this->grade = $grade ?? 1;
        if(!empty($value)){
            $this->value = round($value,2);
        }


    }

    public function print()
    {
        $tooltip = $value = '';
        if(!empty($this->tooltip))
            $tooltip = 'data-tooltip="'.$this->tooltip.'"';
        if(!empty($this->value))
            $value = '<div class="matneed">'.$this->value.'</div>';

        $urlImg = '/img/icons/50/'.$this->icon.'.png';
        $urlGrade = '/img/grade/icon_grade'.$this->grade.'.png';
        ?>
        <div class="itim" id="itim_<?php echo $this->id?>" style="background-image: url(<?php echo $urlImg?>)">
            <div class="grade" <?php echo $tooltip?> style="background-image: url(<?php echo $urlGrade?>)">
                <?php echo $value?>
            </div>
        </div>
        <?php
    }


}