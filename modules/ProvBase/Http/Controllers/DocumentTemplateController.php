<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\DocumentTemplate;

class DocumentTemplateController extends \BaseController
{
    public function view_form_fields($model = null)
    {
        return [
            ['form_type' => 'text', 'file' => 'file', 'description' => 'file'],
            ['form_type' => 'select', 'name' => 'template_file', 'description' => 'Choose template file', 'value' => 'foo'],
            ['form_type' => 'file', 'name' => 'template_upload', 'description' => 'Upload template', 'help' => trans('helper.tex_template')],
        ];
    }
}
