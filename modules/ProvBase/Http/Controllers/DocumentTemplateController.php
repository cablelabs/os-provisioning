<?php

namespace Modules\ProvBase\Http\Controllers;

use Modules\ProvBase\Entities\DocumentTemplate;
use Modules\ProvBase\Entities\DocumentType;

class DocumentTemplateController extends \BaseController
{
    protected $file_upload_paths = [
        'file' => 'app/config/provbase/documenttemplates/',
    ];

    /**
     * @author Patrick Reichel
     */
    public function view_form_fields($model = null)
    {
        if (! $model) {
            $model = new DocumentTemplate;
        }

        if ($model->exists) {
            $id_for_validation = $model->id;
        }
        else {
            $id_for_validation = 0;
        }

        $company_id = \Request::get('company_id', '');
        if ($company_id) {
            $model->company_id = $company_id;
        }
        $sepaaccount_id = \Request::get('sepaaccount_id', '');
        if ($sepaaccount_id) {
            $model->sepaaccount_id = $sepaaccount_id;
        }

        if (\Module::collections()->has('BillingBase')) {
            if ((0 == $model->company_id) && (0 == $model->sepaaccount_id)) {
                $companies = ['0' => trans('provbase::view.documentTemplate.baseTemplate')];
                $sepaaccounts = ['0' => trans('provbase::view.documentTemplate.baseTemplate')];
            }
            else {
                $companies = \Modules\BillingBase\Entities\Company::get_companies_for_edit_view(true);
                $sepaaccounts = \Modules\BillingBase\Entities\SepaAccount::get_sepaaccounts_for_edit_view(true);
            }
            $a = [
                ['form_type' => 'select', 'name' => 'company_id', 'description' => trans('provbase::view.documentTemplate.company'), 'value' => $companies, 'help' => trans('provbase::help.documentTemplate.templateRelation')],
                ['form_type' => 'select', 'name' => 'sepaaccount_id', 'description' => trans('provbase::view.documentTemplate.sepaaccount'), 'value' => $sepaaccounts, 'help' => trans('provbase::help.documentTemplate.templateRelation'), 'space' => 1],
            ];
        }
        else {
            $a = [
                ['form_type' => 'text', 'name' => 'company_id', 'value' => 0, 'hidden' => 1],
                ['form_type' => 'text', 'name' => 'sepaaccount_id', 'value' => 0, 'hidden' => 1],
            ];
        }

        $document_types = DocumentType::get_types_for_edit_view();
        $template_files = self::get_storage_file_list('provbase/documenttemplates/');
        if ($model && $model->filename_pattern) {
            $filename_pattern = $model->filename_pattern;
            $filename_pattern_placeholder = $filename_pattern;
        }
        else {
            $filename_pattern = '';
            $filename_pattern_placeholder = $model->exists ? $model->documenttype->default_filename_pattern : '';
        }

        $b = [
            ['form_type' => 'select', 'name' => 'documenttype_id', 'description' => trans('provbase::view.documentTemplate.type'), 'value' => $document_types],
            ['form_type' => 'text', 'name' => 'filename_pattern', 'description' => trans('provbase::view.documentTemplate.filenamePattern'), 'value' => $filename_pattern, 'help' => trans('provbase::help.documentTemplate.filenamePattern'), 'options' => ['placeholder' => $filename_pattern_placeholder], 'space' => 1],
            ['form_type' => 'select', 'name' => 'file', 'description' => trans('provbase::view.documentTemplate.chooseTemplateFile'), 'value' => $template_files],
            ['form_type' => 'file', 'name' => 'file_upload', 'description' => trans('provbase::view.documentTemplate.uploadTemplateFile'), 'help' => trans('provbase::help.documentTemplate.uploadTemplate')],
            ['form_type' => 'text', 'name' => 'id_for_validation', 'value' => $id_for_validation, 'hidden' => 1],
        ];

        return array_merge($a, $b);
    }

    /**
     * Set not given foreign IDs to null.
     *
     * @author Patrick Reichel
     */
    public function prepare_input($data)
    {
        $data = parent::prepare_input($data);
        $nullable_fields = [
            'company_id',
            'sepaaccount_id',
        ];
        $data = $this->_nullify_fields($data, $nullable_fields);
        return $data;
    }


    /**
     * Adds the company and sepaaccount informations for extended validation
     *
     * @author Patrick Reichel
     */
    public function prepare_rules($rules, $data)
    {
        $rules['documenttype_id'] .= '|template_type_unique:documenttemplate_id,'.$data['id_for_validation'];
        unset($data['id_for_validation']);  // only used to build the rule

        // precedence: SepaAccount>Company>BaseTemplate
        if ($data['sepaaccount_id']) {
            $rules['documenttype_id'] .= ',sepaaccount_id,'.$data['sepaaccount_id'];
        }
        elseif ($data['company_id']) {
            $rules['documenttype_id'] .= ',company_id,'.$data['company_id'];
        }
        else {
            $rules['documenttype_id'] .= ',base,0';
        }

        return parent::prepare_rules($rules, $data);
    }

}

