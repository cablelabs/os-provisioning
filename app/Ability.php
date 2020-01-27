<?php

namespace App;

use Silber\Bouncer\Database\Concerns\IsAbility;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Ability extends Eloquent
{
    use IsAbility;
}
