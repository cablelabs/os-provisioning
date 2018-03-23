<?php

use Models\Qos;

class qualityTest extends TestCase
{

    public function testIndex()
    {
        $this->routeContains();
        $this->routeContains('quality');
    }

    public function testEdit()
    {
        $m = Qos::orderby('id', 'DESC')->first()->id;
        $this->routeContains("quality/$m/edit");
    }

    public function testCreate()
    {
        $this->routeContains("quality/create");
    }

    public function testDelete()
    {
        $m = Qos::orderby('id', 'DESC')->first()->id;
        $this->routeContains("quality/$m", 'DELETE');
    }
}
