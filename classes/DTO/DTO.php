<?php

namespace DTO;

class DTO
{
    protected function bindSelf(object|array $Object): void
    {
        $Object = (object) $Object;
        $vars = get_class_vars($this::class);
        foreach ($vars as $k => $v) {
            if (!isset($Object->$k)) continue;
            $this->$k = $Object->$k;
        }
    }
}