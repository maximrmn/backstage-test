<?php

namespace App\Models;

class RectangularObject extends Container
{
    public $width;
    public $length;
    public $type = Container::RECTANGULAR;

    public function __construct($width, $length) {
        $this->width = $width;
        $this->length = $length;

        parent::__construct($this->width, $this->length);
    }
}
