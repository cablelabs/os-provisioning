<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\Domain;

class DomainController extends \BaseController
{
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'name' => 'name', 'description' => 'URL'],
            ['form_type' => 'text', 'name' => 'alias', 'description' => 'Aliases', 'help' => 'aliases seperated by semicolon'],
            ['form_type' => 'select', 'name' => 'type', 'description' => 'Type', 'value' => Domain::getPossibleEnumValues('type')],
        ];
    }
}
