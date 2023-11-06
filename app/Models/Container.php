<?php

namespace App\Models;

class Container
{
    public $width;
    public $length;
    public $grid = [];

    public const CIRCULAR = 0;
    public const RECTANGULAR = 1;

    public function __construct($width, $length) {
        $this->width = $width;
        $this->length = $length;
        $this->grid = array_fill(0, $this->width, array_fill(0, $this->length, 0));
    }

}
