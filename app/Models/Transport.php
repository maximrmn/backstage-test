<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    public $containers = [];

    public function addCircularContainer($radius)
    {
        $this->containers[] = new CircularObject($radius);
    }

    public function addRectangularContainer($width, $length)
    {
        $this->containers[] = new RectangularObject($width, $length);
    }
}
