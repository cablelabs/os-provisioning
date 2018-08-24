<?php

use Illuminate\Database\Seeder;

abstract class BaseSeeder extends Seeder
{
    protected static $max_seed = 10;
    protected static $max_seed_l2 = 4;	// maximum seed level 2
}
