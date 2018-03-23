<?php

use Models\Modem;

class ModemTest extends TestCase
{
    
    public function testDhcp()
    {
        $m = Modem::first();
        $this->assertTrue($m->make_dhcp_cm_all());
    }

    public function testConfigfile()
    {
        $m = Modem::first();
        $this->assertTrue($m->make_configfile());
        $this->assertTrue($m->make_configfile_all());
    }
    
    public function testIndex()
    {
        $this->routeContains();
        $this->routeContains('modem');
    }

    public function testEdit()
    {
        $m = Modem::first()->id;
        $this->routeContains("modem/$m/edit");
    }

    public function testCreate()
    {
        $this->routeContains("modem/create");
    }

    public function testDelete()
    {
        $m = Modem::orderby('id', 'DESC')->first()->id;
        $this->routeContains("modem/$m", 'DELETE');
    }
}
