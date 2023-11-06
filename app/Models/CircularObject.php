<?php

namespace App\Models;

class CircularObject extends Container
{
    public $radius;
    public $type = Container::CIRCULAR;

    public function __construct($radius) {
        $this->radius = $radius;
        parent::__construct($this->radius * 2, $this->radius * 2);
    }
}
