<?php

namespace Modules\HfcSnmp\Observers;

class ParameterObserver
{
    public function creating($parameter)
    {
        $parameter->divide_by = str_replace([' ', "\t"], '', $parameter->divide_by);
    }

    public function updating($parameter)
    {
        $parameter->divide_by = str_replace([' ', "\t"], '', $parameter->divide_by);
    }
}
