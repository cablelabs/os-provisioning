<?php

namespace Modules\HfcSnmp\Observers;

class IndicesObserver
{
    public function creating($indices)
    {
        $indices->indices = str_replace([' ', "\t"], '', $indices->indices);
    }

    public function updating($indices)
    {
        $indices->indices = str_replace([' ', "\t"], '', $indices->indices);
    }
}
