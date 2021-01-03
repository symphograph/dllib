<?php


class Cubik
{
    public int $id = 0;
    public string $icon = '';
    public int $grade = 1;
    public string $tooltip = '';
    public string $value = '';

    public function __construct(int $id,string $icon, $grade = 1,$tooltip = '',$value = '')
    {
        $this->id = $id;
        $grade = intval($grade);
        $this->grade = $grade ?? 1;
        $this->icon = $icon;
        if(!empty($tooltip))
            $this->tooltip = 'data-tooltip="'.$tooltip.'"';
        if(!empty($value))
            $this->value = '<div class="matneed">'.$value.'</div>';
    }

    public function print()
    {
        $urlImg = '/img/icons/50/'.$this->icon.'.png';
        $urlGrade = '/img/grade/icon_grade'.$this->grade.'.png';
        ?>
        <div class="itim" id="itim_<?php echo $this->id?>" style="background-image: url(<?php echo $urlImg?>)">
            <div class="grade" <?php echo $this->tooltip?> style="background-image: url(<?php echo $urlGrade?>)">
                <?php echo $this->value?>
            </div>
        </div>
        <?php
    }


}