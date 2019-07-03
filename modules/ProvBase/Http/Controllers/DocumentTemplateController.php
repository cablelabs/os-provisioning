<?php

namespace Modules\ProvBase\Http\Controllers;

use Redirect;
use Modules\ProvBase\Entities\DocumentTemplate;
use Modules\ProvBase\Entities\DocumentType;

class DocumentTemplateController extends \BaseController
{
    use \App\AddressFunctionsTrait;

    protected $edit_view_second_button = true;
    protected $second_button_name = null; // set in constructor because of translations
    protected $second_button_title_key = null; // set in constructor because of translations

    protected $edit_view_third_button = true;
    protected $third_button_name = null; // set in constructor because of translations
    protected $third_button_title_key = null; // set in constructor because of translations

    protected $file_upload_paths = [
        'file' => 'app/config/provbase/documenttemplates/',
    ];
    protected $document_base_path = 'config/provbase/documenttemplates';

    /**
     * @author Patrick Reichel
     */
    public function __construct()
    {
        $this->second_button_name = trans('provbase::view.documentTemplate.buttonDownloadTemplate');
        $this->third_button_name = trans('provbase::view.documentTemplate.buttonTestTemplate');

        parent::__construct();
    }

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

        /* d($_POST, $data, $rules); */
        return parent::prepare_rules($rules, $data);
    }

    /**
     * Creaetes LaTeX content from given template and data.
     *
     * @param   DocumentTemplate    $documenttemplate   The template to use
     * @param   array               $data               The data to use (keys are placeholders)
     * @return  string              The LaTeX content to be rendered
     *
     * @author  Patrick Reichel
     */
    protected function _create_tex_file(&$documenttemplate, &$data) {

        $tex = \Storage::get($this->document_base_path.'/'.$documenttemplate->file);

        $tex = $this->_add_letterhead_to_texfile($documenttemplate, $data, $tex);

        foreach ($data as $key => $value) {
            $tex = str_replace('{{'.$key.'}}', $value, $tex);
        }

        return $tex;
    }

    /**
     * Wrapes the LaTeX content in letterhead
     *
     * @param   DocumentTemplate    $documenttemplate   The template to use
     * @param   array               $data               The data to use (keys are placeholders)
     * @param   string              $tex                The LaTeX content to be used
     * @return  string              The LaTeX content to be rendered
     *
     * @author  Patrick Reichel
     */
    protected function _add_letterhead_to_texfile(&$documenttemplate, &$data, $tex)
    {
        // check if given tex is standalone (and not to be wrapped into letterhead)
        if (false === strpos($tex, '\begin{document}')) {
            // check if letterhead for relatedSEPA account
            $letterhead = DocumentTemplate::where('sepaaccount_id', '=', $documenttemplate->sepaaccount_id)->first();
            if (! $letterhead) {
                $letterhead = DocumentTemplate::where('company_id', '=', $documenttemplate->company_id)->first();
            }
            if (! $letterhead) {
                $letterhead = DocumentTemplate::where('company_id', '=', $documenttemplate->company_id)->first();
            }

            $letterhead = \Storage::get($this->document_base_path.'/'.$letterhead->file);
            $texmixed = str_replace('{{Document.content}}', $tex, $letterhead);
            return $texmixed;
        }
        else {
            // nothing to do
            return $tex;
        }
    }


    /**
     * Used to test an uploaded template.
     * This will get random data to be filled in and renders to PDF.
     *
     * @param   int         $template_id    The template to be tested
     *
     * @return  \Redirect   back to edit view; data to be shown is passed in session variable
     *
     * @author Patrick Reichel
     */
    public function test($template_id)
    {
        $documenttemplate = DocumentTemplate::find($template_id);
        $documenttype = $documenttemplate->documenttype;

        $tmpdir = shell_exec('mktemp -d');

        // if null is returned: something went wrong
        if (is_null($tmpdir)) {
            \Session::push('tmp_error_above_form', trans('provbase::messages.documentTemplate.tmpdirCreationFailed'));
            \Log::error('Could not create temporary directory using mktemp');
            return \Redirect::route('DocumentTemplate.edit', $template_id);
        }

        $data = $documenttemplate->collect_data();
        $tmpdir = trim($tmpdir);

        $output_path = $tmpdir;
        $args = [
            /* '-aux-directory='.$aux_path, */
            '-output-directory='.$output_path,
        ];
        $latex = $this->_create_tex_file($documenttemplate, $data);

        // audit created LaTeX
        if (! DocumentTemplate::is_valid_latex($latex)) {
            $ret = [
                'success' => false,
                'url' => null,
                'other_files' => [$documenttemplate->file => $latex],
            ];
            \Session::push('tmp_error_above_form', trans('provbase::messages.documentTemplate.pdfAuditFailed'));
            return \Redirect::route('DocumentTemplate.edit', $template_id)->with('pdf_creation_test', $ret);
        }

        file_put_contents($output_path.'/'.$documenttemplate->file, $latex);
        list($cmd, $pdflatex_output, $retvar) = $this->_pdflatex($output_path, $documenttemplate->file, false, $args, true);

        $pdf = null;
        $file_contents = [];
        foreach (scandir($tmpdir) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            };
            $path = $tmpdir.'/'.$file;
            if (\Str::endswith($file, '.pdf')) {
                $pdf = $path;
            }
            else {
                $file_contents[$file] = file_get_contents($path);
                unlink($path);
            }
        }

        /* $pdf = null; */
        if (is_null($pdf)) {
            \Session::push('tmp_error_above_form', trans('provbase::messages.documentTemplate.pdfCreateTestFailed'));
            $ret = [
                'success' => false,
                'url' => null,
                'other_files' => $file_contents,
            ];

        } else {
            \Session::push('tmp_success_above_form', trans('provbase::messages.documentTemplate.pdfCreateTestSuccess'));
            $ret = [
                'success' => true,
                'url' => \URL::route('DocumentTemplate.download_pdf', base64_encode($tmpdir.'/'.basename($pdf))),
                'other_files' => $file_contents,
            ];

        }
        $ret['cmd'] = $cmd;
        $ret['pdflatex_output'] = implode("\n", $pdflatex_output);
        $ret['usable_placeholders'] = $data;

        return \Redirect::route('DocumentTemplate.edit', $template_id)->with('pdf_creation_test', $ret);
    }

    /**
     * Renders a template – creates a new document.
     *
     * @author Patrick Reichel
     */
    public function render($template_id, &$model=null)
    {

        $documenttemplate = DocumentTemplate::find($template_id);
        $documenttype = $documenttemplate->documenttype;

        /* $data = */

        if ($documenttemplate->format == 'LaTeX') {
            $this->_render_latex($documenttemplate, $model);
        } else {
            throw new \Exception('Rendering of '.$documenttemplate->format.' documenttemplates yet implemented.');
        }
    }

    /**
     * Wrapper to call pdflatex.
     *
     * @author Patrick Reichel
     */
    protected function _render_latex(&$documenttemplate, &$model)
    {

        if (is_null($model)) {
            $model = \Modules\ProvBase\Entities\Contract::find(500010);
            d($model, $this->get_full_german_postal_address($model));
            $path = storage_path('app/'.$this->document_base_path);

            $aux_path = $path.'/workbench';
            $output_path = $path.'/workbench';
            $args = [
                '-aux-directory='.$aux_path,
                '-output-directory='.$output_path,
            ];
            list($errcode, $stdout, $stderr) = pdflatex($path, $documenttemplate->file, false, $args);
            d($ret);
            // this is a test – use seeded data
            throw new \Exception('Not yet implemented.');
        } else {
            // check for letterhead – cannot be used directly in real rendering
            if ('letterhead' == $documenttype->type) {
                \Session::push('tmp_error_above_form', trans('provbase::messages.documentTemplate.letterheadNotUsable'));
                return null;
            }

            $modelname = '\\Modules\\'.$documenttype->module.'\\Entities\\'.$documenttype->model;
            $model = $modelname::find($model_id);

            throw new \Exception('Not yet implemented.');
        }
    }

    /**
     * Calls pdflatex shell command for one .tex file.
     *
     * @param   string  directory the .tex file is stored in
     * @param   string  filename of the .tex file
     * @param   bool    start latex process in background (for faster SettlementRun)
     * @param   array   additional arguments for the pdflatex call (check “man pdflatex” for details)
     *
     * @return  array   consists of [pdflatex_return_code, stdout, stdout]
     *
     * @author Patrick Reichel, inspired by Nino Ryschawy
     *
     * @TODO: As this functionality in future will only be used here: remove from app/helpers
     */
    protected function _pdflatex($src_dir, $src_file, $background=false, $additional_cli_args=[], $test=false)
    {
        chdir($src_dir);

        $default_cli_args = [
            '-interaction=nonstopmode',
            '-no-shell-escape',
        ];
        $pdflatex_executable = '/usr/bin/pdflatex';

        /* NOTE: returns
            * 0 on success
            * 127 if pdflatex is not installed,
            * 134 when pdflatex is called without path /usr/bin/ and path variable is not set when running from cmd line
        */

        if ($test) {
            $cmd = $pdflatex_executable;
            if ($default_cli_args) {
                $cmd .= ' '.implode(' ', $default_cli_args);
            }
            if ($additional_cli_args) {
                $cmd .= ' '.implode(' ', $additional_cli_args);
            }
            $cmd .= ' "'.$src_file.'"';
            $cmd .= ' 2>&1';
            /* $cmd = "/usr/bin/pdflatex -interaction=nonstopmode ".implode(' ', $add_cli_args)." \"$src_file\"  2>&1"; */
            /* $cmd .= $background ? ' &' : ''; */

            $pdflatex_output = [];
            $retvar = null;

            exec($cmd, $pdflatex_output, $retvar);
            return [$cmd, $pdflatex_output, $retvar];
        } else {
            // take care - when we start process in background we don't get the return value anymore
            // according to man page options have to be placed before the filename: pdftex [options] [&format] [file|\commands]
            $cmd = "/usr/bin/pdflatex -interaction=nonstopmode ".implode(' ', $additional_cli_args)." \"$src_file\" &>/dev/null";
            $cmd .= $background ? ' &' : '';

            system($cmd, $ret);

            if ($ret) {
                // log error
                pdflatex_error_msg($ret, true, $dir.$filename);
            }
            return $ret;
        }


    }

    /**
     * Overwrites base method to handle download and test requests, too.
     *
     * @author Patrick Reichel
     */
    public function update($id) {

        // save button pressed ⇒ default save
        if (! \Request::filled('_2nd_action') && ! \Request::filled('_3rd_action')) {
            return parent::update($id);
        }

        // download button pressed
        if (\Request::filled('_2nd_action')) {
            $template = DocumentTemplate::find($id);
            $filepath = $this->document_base_path.'/'.$template->file;

            $file = \Storage::get($filepath);

            $response = \Response::make($file, 200);
            if ($template->format == 'LaTeX') {
                $mime_type = 'text/x-tex';
            } else {
                $mime_type = null;
            }
            if ($mime_type) {
                $response->header('Content-Type', $mime_type);
            }
            $response->header('Content-Disposition', 'attachment; filename="'.$template->file.'"');

            return $response;
        }

        // test button pressed ⇒ render the template
        if (\Request::filled('_3rd_action')) {
            return $this->test($id);
        }
    }

    public function download_pdf($b64_file) {

        $file = base64_decode($b64_file);
        return \Response::download($file, basename($file))->deleteFileAfterSend(true);
    }
}

