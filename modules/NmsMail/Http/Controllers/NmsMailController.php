<?php

namespace Modules\NmsMail\Http\Controllers;

use Nwidart\Modules\Routing\Controller;

class NmsMailController extends Controller
{
    public function index()
    {
        return view('mail::index');
    }
}
