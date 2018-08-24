<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as LaravelCoreController;

abstract class Controller extends LaravelCoreController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
