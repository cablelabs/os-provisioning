<?php

use Models\Phonenumber;

class PhonenumberTest extends TestCase
{

    public function testIndex()
    {
        $this->routeContains();
        $this->routeContains('phonenumber');
    }

    public function testEdit()
    {
        $m = Phonenumber::first()->id;
        $this->routeContains("phonenumber/$m/edit");
    }

    public function testCreate()
    {
        $this->routeContains("phonenumber/create");
    }

    public function testDelete()
    {
        $m = Phonenumber::orderby('id', 'DESC')->first()->id;
        $this->routeContains("phonenumber/$m", 'DELETE');
    }
}
