<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Http\Controllers;

use App\GlobalConfig;
use App\V1\Repository;
use App\V1\Service;
use App\V1\V1Trait;
use Auth;
use BaseModel;
use Bouncer;
use Cache;
use Illuminate\Support\Facades\File;
use Log;
use Modules\CoreMon\Helpers\AlertmanagerApi;
use Monolog\Logger;
use Nwidart\Modules\Facades\Module;
use Redirect;
use Request;
use Session;
use Str;
use Validator;
use View;
use Yajra\DataTables\DataTables;

/*
 * BaseController: The Basic Controller in our MVC design.
 */
class BaseController extends Controller
{
    use V1Trait;
    /*
     * Default VIEW styling options
     * NOTE: All these values could be used in the inheritances classes
     */
    protected $edit_view_save_button = true;
    protected $save_button_name = 'Save';
    // key in messages language file
    protected $save_button_title_key = null;

    protected $edit_view_second_button = false;
    protected $second_button_name = 'Missing action name';
    protected $second_button_title_key = null;
    protected $second_button_icon = null;

    protected $edit_view_third_button = false;
    protected $third_button_name = 'Missing action name';
    protected $third_button_title_key = null;
    protected $third_button_icon = null;

    protected $printButton = false;

    protected $relation_create_button = 'Create';

    // if set to true a create button on index view is available
    protected $index_create_allowed = true;
    protected $index_delete_allowed = true;

    protected $edit_left_md_size = 8;
    protected $index_left_md_size = 12;
    protected $edit_right_md_size = null;

    protected $defaultMdSizes = [
        'leftLeftLg' => 3,
        'leftLeftXl' => 2,
        'rightRightLg' => 3,
        'rightRightXl' => 2,
    ];

    protected $index_tree_view = false;

    /**
     * Placeholder for Many-to-Many-Relation multiselect fields that should be handled generically (e.g. users of Ticket)
     * If special Abilities are needed to edit the valies, place classname in key like:
     * [ App\User::class => 'users_ids']
     * NOTE: When model is deleted all pivot entries will be detached and special handling in BaseModel@delete is omitted
     */
    protected $many_to_many = [];

    /**
     * File upload paths to handle file upload fields generically - see e.g. CompanyController, SepaAccountController
     *
     * NOTE: upload field has to be named like the corresponding select field of the upload field
     *
     * @var array ['upload_field' => 'relative storage path']
     */
    protected $file_upload_paths = [];

    /**
     * Constructor
     *
     * Basically this is a placeholder for eventually later use. I need to
     * overwrite the constructor in a subclass – and want to call the parent
     * constructor if there are changes in base classes. But calling the
     * parent con is only possible if it is explicitely defined…
     *
     * @author Patrick Reichel
     */
    public function __construct()
    {
        // place your code here
    }

    /**
     * Base Function containing the default tabs for each model
     * Overwrite/Extend this function in child controller to add more tabs refering to new pages
     * Use models view_has_many() to add/structure panels inside separate tabs
     *
     * @param model: the model object to be displayed
     *
     * @return: array of tab descriptions - e.g. [['name' => '..', 'route' => '', 'link' => [$model->id]], .. ]
     *
     * @author: Torsten Schmidt, Nino Ryschawy
     */
    protected function editTabs($model)
    {
        $class = get_class($model);

        if (Str::contains($class, 'GuiLog')) {
            return [[
                'name' => 'Log',
                'icon' => 'pencil',
            ]];
        }

        $class_name = $model->get_model_name();

        return [
            [
                'name' => 'Edit',
                'icon' => 'pencil',
            ],
            [
                'name' => 'Logging',
                'route' => 'GuiLog.filter',
                'icon' => 'history',
                'link' => ['model_id' => $model->id, 'model' => $class_name],
            ],
        ];
    }

    public static function get_model_obj()
    {
        $classname = NamespaceController::get_model_name();

        // Rewrite model to check with new assigned Model
        if (! $classname) {
            return;
        }

        if (! class_exists($classname)) {
            return;
        }

        $obj = new $classname;

        return $obj;
    }

    public static function get_controller_obj()
    {
        $classname = NamespaceController::get_controller_name();

        if (! $classname) {
            return;
        }

        if (! class_exists($classname)) {
            return;
        }

        $obj = new $classname;

        return $obj;
    }

    public static function get_config_modules()
    {
        $modules = Module::allEnabled();
        $links = ['Global Config' => 'GlobalConfig'];

        foreach ($modules as $module) {
            $mod_path = explode('/', $module->getPath());
            $tmp = end($mod_path);

            $mod_controller_name = 'Modules\\'.$tmp.'\\Http\\Controllers\\'.$tmp.'Controller';
            $mod_controller = new $mod_controller_name;

            if (method_exists($mod_controller, 'view_form_fields')) {
                $links[($module->get('alias') == '') ? $tmp : $module->get('alias')] = $tmp;
            }
        }
        // Sla (service level agreement) is not a separate module, but belongs to GlobalConfig
        $links['Sla'] = 'Sla';

        return $links;
    }

    /**
     * Set all nullable field without value given to null.
     * Use this to e.g. set dates to null (instead of 0000-00-00).
     *
     * Call this method on demand from your prepare_input()
     *
     * @author Patrick Reichel
     *
     * @param $nullable_fields array containing fields to check
     */
    protected function _nullify_fields($data, $nullable_fields = [])
    {
        foreach ($nullable_fields as $field) {
            if (isset($data[$field]) && ! $data[$field]) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * Returns a default input data array, that shall be overwritten
     * from the appropriate model controller if needed.
     *
     * Note: Will be running before Validation
     *
     * Tasks: Checkbox Entries will automatically set to 0 if not checked
     */
    protected function prepare_input($data)
    {
        // Checkbox Unset ?
        foreach ($this->view_form_fields(static::get_model_obj()) as $field) {
            // skip file upload fields
            if ($field['form_type'] == 'file') {
                continue;
            }

            // Checkbox Unset ?
            if (! isset($data[$field['name']]) && ($field['form_type'] == 'checkbox')) {
                $data[$field['name']] = 0;
            }

            // JavaScript controlled checkboxes sometimes returns “on” if checked – which results in
            // logical false (=0) in database so we have to overwrite this by 1
            // this is e.g. the case for the active checkbox on ProvVoip\Phonenumber
            // the value in $_POST seems to be browser dependend – extend the array if needed
            if (
                ($field['form_type'] == 'checkbox') &&
                in_array(Str::lower($data[$field['name']]), ['on', 'checked'])
            ) {
                $data['active'] = '1';
            }

            // multiple select?
            if ($field['form_type'] == 'select' && isset($field['options']['multiple'])) {
                $field['name'] = str_replace('[]', '', $field['name']);
                continue; 			// multiselects will have array in data so don't trim
            }
        }

        return $data;
    }

    /**
     * Normalizes numeric values to minimize problems for e.g. German users (using a comma instead a dot in float).
     *
     * @param $value the numeric string to normalize
     *
     * @author Patrick Reichel
     */
    protected function normalizeNumericString($value)
    {
        // take care of nullable fields
        if (is_null($value)) {
            return;
        }

        // Germans use comma as decimal separator – replace by dot
        $value = str_replace(',', '.', $value);

        return $value;
    }

    /**
     * Returns a default input data array, that shall be overwritten
     * from the appropriate model controller if needed.
     *
     * Note: Will be running _after_ Validation
     */
    protected function prepare_input_post_validation($data)
    {
        return $data;
    }

    /**
     * Returns an array of validation rules in dependence of the formular Input data
     * of the http request, that shall be overwritten from the appropriate model
     * controller if needed.
     *
     * Note: Will be running before Validation
     */
    protected function prepare_rules($rules, $data)
    {
        return $rules;
    }

    /**
     * Prepare tabs for edit page
     * Merge defined tabs from editTabs() and view_has_many()
     *
     * @author Nino Ryschawy
     *
     * @param relations  from view_has_many()
     * @param tabs       from editTabs()
     * @return array tabs for split-no-panel.blade and edit.blade
     */
    protected function prepare_tabs($relations, $tabs)
    {
        // Generate tabs from array structure of relations
        foreach (array_keys($relations) as $tab) {
            if (! $this->tabDefined($tab, $tabs)) {
                $tabs[] = [
                    'name' => $tab,
                    'icon' => $relations[$tab]['icon'] ?? '',
                ];
            }
        }

        return $tabs;
    }

    /**
     * Check if tab of relations (defined in view_has_many()) is already defined in tabs from editTabs()
     *
     * @return bool
     */
    private function tabDefined($relationsTab, $editTabs)
    {
        foreach ($editTabs as $relation) {
            if ($relation['name'] == $relationsTab) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle file uploads.
     * - check if a file is uploaded
     * - if so:
     *   - move file to dst_path
     *   - overwrite base_field in Input with filename
     *
     * @param base_field Input field to be processed
     * @param dst_path Path to move uploaded file in
     *
     * @author Patrick Reichel
     */
    protected function handle_file_upload($base_field, $dst_path)
    {
        $upload_field = $base_field.'_upload';

        if (! Request::hasFile($upload_field)) {
            return;
        }

        // get filename
        $filename = Request::file($upload_field)->getClientOriginalName();

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $fn = pathinfo($filename, PATHINFO_FILENAME);
        $filename = sanitize_filename($fn).".$ext";

        // move file
        Request::file($upload_field)->move($dst_path, $filename);

        // place filename as chosen value in Input field
        Request::merge([$base_field => $filename]);

        if (Module::collections()->has('ProvBase') && $base_field == 'firmware' && Request::get('device') == 'tr069') {
            // file upload using curl_file_create and method PUT adds headers
            // Content-Disposition, Content-Type and boundaries, which corrupts
            // the file to be uploaded, thus call curl from command line
            /*
            \Modules\ProvBase\Entities\Modem::callGenieAcsApi("files/$filename", 'PUT',
                ['file' => curl_file_create("/tftpboot/fw/$filename")],
                ['Content-Type: application/x-www-form-urlencoded']
            );
            */
            exec("curl -i \"http://localhost:7557/files/$filename\" -X PUT --data-binary @\"/tftpboot/fw/$filename\"");
        }

        return $filename;
    }

    /**
     * Accessor for File Upload Paths
     *
     * @return array
     */
    protected function getFileUploadPaths(): array
    {
        return $this->file_upload_paths;
    }

    /**
     * Handle file uploads generically in store and update function
     *
     * NOTE: use Accessor getFileUploadPaths() or protected property file_upload_paths
     * in Controller to specify DB column and storage path
     *
     * @param array 	Input data array passed by reference
     */
    protected function doFileUploads(&$data)
    {
        foreach ($this->getFileUploadPaths() as $column => $path) {
            $filename = $this->handle_file_upload($column, storage_path($path));

            if ($filename !== null) {
                $data[$column] = $filename;
            }
        }
    }

    /**
     * Set required default Variables for View
     * Use it like:
     *   View::make('Route.Name', $this->compact_prep_view(compact('ownvar1', 'ownvar2')));
     *
     * @author Torsten Schmidt
     */
    public function compact_prep_view()
    {
        $a = func_get_args()[0];

        $a['user'] = \App\User::where('id', auth()->id())
            ->withCount('unreadNotifications')
            ->with([
                'unreadNotifications' => function ($query) {
                    $query->orderByDesc('created_at');
                },
            ])
            ->first();

        $model = static::get_model_obj();

        if (! $model) {
            $model = new BaseModel;
        }

        if (! isset($a['action'])) {
            $a['action'] = 'update';
        }

        if (! isset($a['networks'])) {
            $a['networks'] = [];
            if (Module::collections()->has('HfcBase') && Bouncer::can('view', \Modules\HfcBase\Entities\TreeErd::class)) {
                $a['networks'] = \Modules\HfcReq\Entities\NetElement::getSidebarNets();
                $a['netCount'] = $a['networks']->count();
                $a['favorites'] = auth()->user()->favNetelements()->pluck('netelement.id');
            }
        }

        if (Module::collections()->has('CoreMon')) {
            // get favorite Market NetElement
            $marketNetelement = auth()->user()->favNetelements()->where('base_type_id', 16)->first();

            $a['quick_view_network'] = $marketNetelement ? cache()->remember("Marketstatistic-$marketNetelement->id", 5 * 60, function () use ($marketNetelement) {
                return $this->alarmsNetElement($marketNetelement, true);
            }) : null;
        }

        if (! isset($a['view_header_links'])) {
            $a['view_header_links'] = BaseViewController::view_main_menus();
        }

        if (! isset($a['route_name'])) {
            $a['route_name'] = NamespaceController::get_route_name();
        }

        if (! isset($a['ajax_route_name'])) {
            $a['ajax_route_name'] = $a['route_name'].'.data';
        }

        if (! isset($a['model_name'])) {
            $a['model_name'] = NamespaceController::get_model_name();
        }

        if (! isset($a['view_header'])) {
            $a['view_header'] = $model->view_headline();
        }

        if (! isset($a['view_no_entries'])) {
            $a['view_no_entries'] = $model->view_no_entries();
        }

        if (! isset($a['headline'])) {
            $a['headline'] = '';
        }

        if (! isset($a['form_update'])) {
            $a['form_update'] = NamespaceController::get_route_name().'.update';
        }

        if (! isset($a['edit_left_md_size'])) {
            $a['edit_left_md_size'] = $this->edit_left_md_size;
        }

        if (! isset($a['index_left_md_size'])) {
            $a['index_left_md_size'] = $this->index_left_md_size;
        }

        if (! isset($a['mdSizes'])) {
            $a['mdSizes'] = $this->defaultMdSizes;
        }

        if (! is_null($this->edit_right_md_size) && ! isset($a['edit_right_md_size'])) {
            $a['edit_right_md_size'] = $this->edit_right_md_size;
        }

        if (! isset($a['html_title'])) {
            $a['html_title'] = 'NMS Prime - '.BaseViewController::translate_view(NamespaceController::module_get_pure_model_name(), 'Header');
        }

        if (Module::collections()->has('ProvVoipEnvia') && (! isset($a['envia_interactioncount']))) {
            $a['envia_interactioncount'] = \Modules\ProvVoipEnvia\Entities\EnviaOrder::get_user_interaction_needing_enviaorder_count();
        }

        if (Module::collections()->has('Dashboard')) {
            $a['modem_statistics'] = \Modules\Dashboard\Http\Controllers\DashboardController::get_modem_statistics();
        }

        if (! isset($a['view_help'])) {
            $a['view_help'] = $this->view_help();
        }

        if (! isset($a['externalApps'])) {
            $a['externalApps'] = $this->getExternalApps();
        }

        $a['edit_view_save_button'] = $this->edit_view_save_button;
        $a['save_button_name'] = $this->save_button_name;
        $a['second_button_name'] = $this->second_button_name;
        $a['edit_view_second_button'] = $this->edit_view_second_button;
        $a['second_button_title_key'] = $this->second_button_title_key;
        $a['second_button_icon'] = $this->second_button_icon;
        $a['third_button_name'] = $this->third_button_name;
        $a['third_button_icon'] = $this->third_button_icon;
        $a['edit_view_third_button'] = $this->edit_view_third_button;
        $a['third_button_title_key'] = $this->third_button_title_key;
        $a['save_button_title_key'] = $this->save_button_title_key;
        $a['printButton'] = $this->printButton;
        $a['nmsprimeLogoLink'] = Module::collections()->has('Dashboard') ? route('Dashboard.index') : '';

        // Get Framework Informations
        $a['globalConfig'] = Cache::rememberForever('GlobalConfig', function () {
            return GlobalConfig::first();
        });
        $a['version'] = $a['globalConfig']->version();

        return $a;
    }

    /**
     * Create array of external apps.
     *
     * @author Roy Schneider
     *
     * @return array $apps
     */
    public function getExternalApps()
    {
        $cachedApps = cache('externalApps');
        if ($cachedApps) {
            return $cachedApps;
        }

        $apps = config('externalApps');

        foreach ($apps as $name => $value) {
            $package = exec('rpm -q '.escapeshellarg($value['rpmName']));
            $apps[$name]['state'] = \Str::contains($package, 'not installed') ? 'inactive' : 'active';
        }

        Cache::forever('externalApps', $apps);

        return $apps;
    }

    /**
     * Perform a global search.
     *
     * @return Illuminate\Support\Facades\View
     *
     * @author Roy Schneider
     */
    protected function globalSearch($fromTags = null)
    {
        $query = Request::get('query');
        $view_header = 'Global Search';
        $basemodel = new BaseModel;

        // search for tags?
        if ($searchTag = $this->getGlobalSearchQuery($query)) {
            $query = $searchTag[2];
        }

        $models = collect($basemodel->get_models())->reject(function ($class) {
            return Bouncer::cannot('view', $class);
        })->map(function ($name) {
            return new $name;
        });

        $view_var = collect($fromTags);
        if (! $searchTag) {
            try {
                foreach ($this->globalSearchResults($query, $models) as $result) {
                    if ($result->isNotEmpty()) {
                        $view_var = $view_var->merge($result);
                    }
                }
            } catch (\Exception $e) {
                //
            }
        }

        $view_var = $view_var->unique('id');
        $results = count($view_var);

        return View::make('Generic.searchglobal', $this->compact_prep_view(compact('view_header', 'view_var', 'query', 'results')));
    }

    /**
     * Remove tag (like 'ip:') from query and return both.
     *
     * @param $query String
     * @return array|null
     *
     * @author Roy Schneider
     */
    protected function getGlobalSearchQuery($query)
    {
        preg_match('/(^[a-zA-Z]+:)(.*)/', $query, $parts);

        if (array_key_exists(2, $parts) && $parts[2] != '') {
            return $parts;
        }
    }

    /**
     * Get all models where a specific column exists.
     *
     * @param $attribute String
     * @param $name String
     * @return stdClass
     *
     * @author Roy Schneider
     */
    protected function getTableWithColumn($attribute, $name)
    {
        $tables = \DB::select("SELECT table_name, column_name FROM information_schema.columns WHERE column_name='$name';");
        $devices = [];

        foreach ($tables as $table) {
            $model = \BaseModel::_guess_model_name($table->table_name);
            $hasAttribute = $model::where($name, $attribute)->first();
            if ($hasAttribute) {
                $devices[] = $hasAttribute;
            }
        }

        return $devices;
    }

    /**
     * Search for $query in all models.
     *
     * @param $query String query to search for
     * @param $models Illuminate\Support\Collection with models to search in
     * @return $result array of collections of models with $query in any column
     *
     * @author Roy Schneider
     */
    protected function globalSearchResults($query, $models)
    {
        if ($query == '') {
            return collect();
        }

        // necessary because of the concatenation of all table rows
        $query = str_replace('*', '%', $query);
        if (! Str::startsWith($query, '%')) {
            $query = '%'.$query;
        }

        if (! Str::endsWith($query, '%')) {
            $query = $query.'%';
        }

        $results = [];
        foreach ($models as $model) {
            if (! property_exists($model, 'table') || ! \Schema::hasTable($model->getTable())) {
                continue;
            }

            $queryResult = $model::whereRaw("CONCAT_WS('|', ".$model::getTableColumns($model->getTable()).') LIKE ?', [$query])->limit(100);

            if ($queryResult) {
                $results[] = $queryResult->get();
            }
        }

        return $results;
    }

    /**
     * Overwrite this method in your controllers to inject additional data in your edit view
     * Default is an empty array that simply will be ignored on generic views
     *
     * For an example view EnviaOrder and their edit.blade.php
     *
     * @author Patrick Reichel
     *
     * @return data to be injected; should be an array
     */
    protected function getAdditionalDataForEditView($model)
    {
        return [];
    }

    /**
     * Use this in your Controller->view_form_fields() methods to add a first [0 => ''] in front of your options coming from database
     * Don't use array_merge for this topic as this will reassing numerical keys and in doing so destroying the mapping of database IDs!!
     *
     * Watch ProductController for a usage example.
     *
     * @author Patrick Reichel
     *
     * @param $options the options array generated from database
     * @param $first_value value to be set at $options[0] – defaults to empty string
     * @return $options array with 0 element on first position
     */
    protected function _add_empty_first_element_to_options($options, $first_value = '')
    {
        $ret = [null => $first_value];

        foreach ($options as $key => $value) {
            $ret[$key] = $value;
        }

        return $ret;
    }

    /**
     * Display a listing of all objects of the calling model
     *
     * @return View
     */
    public function index()
    {
        $model = static::get_model_obj();
        $headline = BaseViewController::translate_view($model->view_headline(), 'Header', 2);
        $view_header = BaseViewController::translate_view('Overview', 'Header');
        $create_allowed = static::get_controller_obj()->index_create_allowed;
        $delete_allowed = static::get_controller_obj()->index_delete_allowed;

        if ($this->index_tree_view) {
            // TODO: remove orWhere statement when it is sure that parent_id is nullable and can not be 0 in all NMSPrime instances and after new installation!!!
            $view_var = $model::whereNull('parent_id')->orWhere('parent_id', 0);

            if (method_exists($model, 'children')) {
                $view_var->with('children');
            }

            $view_var = $view_var->get();

            $undeletables = $model::undeletables();

            return View::make('Generic.tree', $this->compact_prep_view(compact('headline', 'view_header', 'view_var', 'create_allowed', 'undeletables')));
        }

        $filter = $model::storeIndexFilterIntoSession();
        $viewName = NamespaceController::get_view_name();
        $view_path = View::exists($viewName.'.index') ? $viewName.'.index' : 'Generic.index';

        $methodExists = method_exists($model, 'view_index_label');
        $indexTableInfo = $methodExists ? $model->view_index_label() : [];
        $hugeTable = $model->hasHugeIndexTable();

        Log::debug('Showing only index() elements a user can access is not yet implemented');

        return View::make($view_path, $this->compact_prep_view(compact('create_allowed', 'delete_allowed',
            'filter', 'headline', 'hugeTable', 'indexTableInfo', 'methodExists', 'model', 'view_header')));
    }

    /**
     * Show the form for creating a new model item
     *
     * @return View
     */
    public function create()
    {
        $model = static::get_model_obj();
        $action = 'create';

        $view_header = BaseViewController::translate_view($model->view_headline(), 'Header');
        $headline = BaseViewController::compute_headline(NamespaceController::get_route_name(), $view_header, null, $_GET);
        $fields = BaseViewController::prepare_form_fields(static::get_controller_obj()->view_form_fields($model), $model);
        $form_fields = BaseViewController::add_html_string($fields, 'create');
        // $form_fields = BaseViewController::add_html_string (static::get_controller_obj()->view_form_fields($model), $model, 'create');

        $view_path = 'Generic.create';
        $form_path = 'Generic.form';

        // proof if there is a special view for the calling model
        if (View::exists(NamespaceController::get_view_name().'.create')) {
            $view_path = NamespaceController::get_view_name().'.create';
        }
        if (View::exists(NamespaceController::get_view_name().'.form')) {
            $form_path = NamespaceController::get_view_name().'.form';
        }

        return View::make($view_path, $this->compact_prep_view(compact('view_header', 'form_fields', 'form_path', 'headline', 'action')));
    }

    /**
     * API equivalent of create()
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_create($ver)
    {
        if ($ver !== '0') {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }

        $model = static::get_model_obj();
        $fields = BaseViewController::prepare_form_fields(static::get_controller_obj()->view_form_fields($model), $model);
        $fields = $this->apiHandleHtmlFields($fields);

        // Set key-by-name and rename to models to unify with api_get / api_index
        $models = collect($fields)->keyBy('name');

        return response()->v0ApiReply(compact('models'), true);
    }

    /**
     * As form fields with form_type => 'html' can have multiple Input fields these have to be extracted for the API
     *  This replaces the html form field by multiple fields expressing the input fields that the html field contains
     *
     * @author Nino Ryschawy
     *
     * @return array
     */
    private function apiHandleHtmlFields($fields)
    {
        foreach ($fields as $key => $field) {
            if (! (isset($field['form_type']) && $field['form_type'] == 'html' && isset($field['html']))) {
                continue;
            }

            preg_match_all('/<input.*?>/', $field['html'], $matches);

            if (! $matches) {
                continue;
            }

            foreach ($matches[0] as $input) {
                preg_match('/name=(.*?) /', $input, $name);

                if (! $name) {
                    $name = $field['name'] ?? 'without name';
                    Log::error("Name of input field $name of view_form_fields missing");

                    continue;
                }

                $name = str_replace(['"', "'"], '', $name[1]);

                $field['name'] = $name;

                if (count($matches[0]) > 1) {
                    $field['description'] .= ' '.$name;
                }

                $fields[] = $field;
            }

            unset($fields[$key]);
        }

        return $fields;
    }

    /**
     * Generic store function - stores an object of the calling model
     *
     * @param redirect: if set to false returns id of the new created object (default: true)
     *
     * @return: html redirection to edit page (or if param $redirect is false the new added object id)
     */
    public function store($redirect = true)
    {
        $obj = static::get_model_obj();
        $controller = static::get_controller_obj();

        // Prepare and Validate Input
        // Note: prepare_input must be before prepare_rules as functionality in some controllers depend on it (e.g. IpPoolController@prepare_rules)
        $data = $controller->prepare_input(Request::all());
        $rules = $controller->prepare_rules($obj->rules(), $data);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Log::info('Validation Rule Error: '.$validator->errors());

            $msg = trans('validation.invalid_input');
            $obj->addAboveMessage($msg, 'error', 'form');

            return Redirect::back()->withErrors($validator)->withInput();
        }
        $data = $controller->prepare_input_post_validation($data);

        // Handle file uploads generically - this must happen after the validation as moving the file before leads always to validation error
        $this->doFileUploads($data);

        $obj = $obj::create($data);

        // Add N:M Relations
        $this->_set_many_to_many_relations($obj, $data);

        $id = $obj->id;
        if (! $redirect) {
            return $id;
        }

        $msg = trans('messages.created');
        $obj->addAboveMessage($msg, 'success', 'form');

        return Redirect::route(NamespaceController::get_route_name().'.edit', $id)->with('message', $msg)->with('message_color', 'success');
    }

    /**
     * API equivalent of store()
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_store($ver)
    {
        if ($ver === '0') {
            $obj = static::get_model_obj();
            $controller = static::get_controller_obj();

            // Prepare and Validate Input
            $data = $this->_api_prepopulate_fields($obj, $controller);
            $data = $controller->prepare_input($data);
            $rules = $controller->prepare_rules($obj->rules(), $data);
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->v0ApiReply(['validations' => $validator->errors()], false, $obj->id);
            }

            $data = $controller->prepare_input_post_validation($data);

            $obj = $obj::create($data);

            // Add N:M Relations
            self::_set_many_to_many_relations($obj, $data);

            return response()->v0ApiReply([], true, $obj->id);
        } elseif ($ver === '1') {
            $data = Request::all();
            $model = (new Service(new Repository(static::get_model_obj())))->create($data);

            return $this->response($model, 200);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    /**
     * Show the editing form of the calling Object
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $model = static::get_model_obj();
        $view_var = $model->findOrFail($id);
        $view_var->loadEditViewRelations();

        $view_header = BaseViewController::translate_view($model->view_headline(), 'Header');
        $headline = BaseViewController::compute_headline(NamespaceController::get_route_name(), $view_header, $view_var);

        $fields = BaseViewController::prepare_form_fields(static::get_controller_obj()->view_form_fields($view_var), $view_var);
        $form_fields = BaseViewController::add_html_string($fields, 'edit');

        // view_has_many should actually be a controller function!
        $relations = $view_var->view_has_many();
        $tabs = collect($this->prepare_tabs($relations, $this->editTabs($view_var)));
        $firstTab = $tabs->reject(fn ($tab) => isset($tab['route']))->first();
        if ($firstTab) {
            $firstTab = $firstTab['name'];
        }

        // check if there is additional data to be passed to blade template
        // on demand overwrite base method getAdditionalDataForEditView($model)
        $additional_data = $this->getAdditionalDataForEditView($view_var);

        $view_path = 'Generic.edit';
        $form_path = 'Generic.form';

        // proof if there are special views for the calling model
        if (View::exists(NamespaceController::get_view_name().'.edit')) {
            $view_path = NamespaceController::get_view_name().'.edit';
        }
        if (View::exists(NamespaceController::get_view_name().'.form')) {
            $form_path = NamespaceController::get_view_name().'.form';
        }

        return View::make($view_path, $this->compact_prep_view(compact('view_var', 'view_header', 'form_path', 'form_fields', 'headline', 'tabs', 'firstTab', 'relations', 'additional_data')));
    }

    /**
     * Update the specified data in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $obj = static::get_model_obj()->findOrFail($id);
        $controller = static::get_controller_obj();

        // Prepare and Validate Input
        $data = $controller->prepare_input(Request::all());
        $data['id'] = $obj->id = $id;
        $rules = $controller->prepare_rules($obj->rules(), $data);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Log::info('Validation Rule Error: '.$validator->errors());

            $msg = trans('validation.invalid_input');
            $obj->addAboveMessage($msg, 'error', 'form');

            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Handle file uploads generically - this must happen after the validation as moving the file before leads always to validation error
        $this->doFileUploads($data);
        $data = $controller->prepare_input_post_validation($data);

        // update timestamp, this forces to run all observer's
        // Note: calling touch() forces a direct save() which calls all observers before we update $data
        //       when exit in middleware to a new view page (like Modem restart) this kill update process
        //       so the solution is not to run touch(), we set the updated_at field directly
        $data['updated_at'] = now();

        // Note: Eloquent Update requires updated_at to either be in the fillable array or to have a guarded field
        //       without updated_at field. So we globally use a guarded field from now, to use the update timestamp
        $updated = $obj->update($data);

        if ($updated) {
            // Add N:M Relations
            if (isset($this->many_to_many) && is_array($this->many_to_many)) {
                $this->_set_many_to_many_relations($obj, $data);
            }
        }

        // create messages depending on error state created while observer execution
        // TODO: check if giving msg/color to route is still wanted or obsolete by the new tmp_*_above_* messages format
        if ($updated && (! Session::has('error'))) {
            $msg = 'Updated!';
            $obj->addAboveMessage($msg, 'success', 'form');
        } else {
            $msg = Session::get('error');
            $obj->addAboveMessage($msg, 'error', 'form');
        }

        $route_model = NamespaceController::get_route_name();

        if (in_array($route_model, self::get_config_modules())) {
            return Redirect::route('Config.index');
        }

        return Redirect::route($route_model.'.edit', $id);
    }

    /**
     * API equivalent of update()
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_update($ver, $id)
    {
        if ($ver === '0') {
            $obj = static::get_model_obj()->findOrFail($id);
            $controller = static::get_controller_obj();

            // Prepare and Validate Input
            $data = $this->_api_prepopulate_fields($obj, $controller);
            $data['id'] = $obj->id = $id;
            $data = $controller->prepare_input($data);
            $rules = $controller->prepare_rules($obj->rules(), $data);
            $validator = Validator::make($data, $rules);
            $data = $controller->prepare_input_post_validation($data);

            if ($validator->fails()) {
                return response()->v0ApiReply(['validations' => $validator->errors()], false, $obj->id);
            }

            $data['updated_at'] = now();

            $obj->update($data);

            // Add N:M Relations
            self::_set_many_to_many_relations($obj, $data);

            return response()->v0ApiReply([], true, $obj->id);
        } elseif ($ver === '1') {
            $data = Request::all();
            $model = (new Service(new Repository(static::get_model_obj())))->update($id, $data);

            return $this->response($model, 200);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    /**
     * Prepopluate all data fields of the corresponding object, so that an API
     * request only needs to send the fields which should be updated and not all
     *
     * @author Ole Ernst
     *
     * @return array
     */
    private function _api_prepopulate_fields($obj, $ctrl)
    {
        $fields = BaseViewController::prepare_form_fields($ctrl->view_form_fields(clone $obj), $obj);
        $fields = $this->apiHandleHtmlFields($fields);
        $inputs = Request::all();
        $data = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $data[$name] = Request::has($name) ? Request::input($name) : $field['field_value'];
        }

        return $data;
    }

    /**
     * Store Many to Many Relations in Pivot table
     *
     * IMPORTANT NOTES:
     *	 To assign a model to the pivot table we need an extra multiselect field in the controllers
     *	 	view_form_fields() that must be mentioned inside the guarded array of the model and the many_to_many array of the Controller!!
     *	 The multiselect's field name must be in form of the models relation function and a concatenated '_ids'
     *		like: '<relation-function>_ids' , e.g. 'users_ids' for the multiselect in Tickets view to assign users
     *
     * @param object 	The Object to store/update
     * @param array 	Input Data
     *
     * @author Nino Ryschawy
     */
    private function _set_many_to_many_relations($obj, $data)
    {
        if (Bouncer::cannot('update', get_class($obj))) {
            return;
        }

        foreach ($this->many_to_many as $key => $field) {
            if (isset($field['classes']) &&
                (Bouncer::cannot('update', $field['classes'][0]) || Bouncer::cannot('update', $field['classes'][1]))) {
                Session::push('error', "You are not allowed to edit {$field['classes'][0]} or {$field['classes'][1]}");
                continue;
            }

            if (! isset($data[$field['field']])) {
                $data[$field['field']] = [];
            }

            $changed_attributes = collect();
            $eloquent_relation_function = explode('_', $field['field'])[0];
            $eloquent_relation = $obj->$eloquent_relation_function();
            $attached_entities = $eloquent_relation->get();
            $attached_ids = $attached_entities->pluck('id')->all();

            // attach new assignments
            foreach ($data[$field['field']] as $form_id) {
                if ($form_id && ! in_array($form_id, $attached_ids)) {
                    $eloquent_relation->attach($form_id, ['created_at' => date('Y-m-d H:i:s')]);

                    $attribute = $attached_entities->where('id', '=', $form_id)->first();

                    $attribute = $attribute->name ?? $attribute->login_name ?? 'id '.$form_id;
                    $attribute .= ' attached';
                    $changed_attributes->push($attribute);
                }
            }

            // detach removed assignments (from selected multiselect options)
            foreach ($attached_ids as $foreign_id) {
                if (! in_array($foreign_id, $data[$field['field']])) {
                    $removed_entity = $attached_entities->where('id', '=', $foreign_id)->first();
                    $eloquent_relation->detach($foreign_id);

                    $attribute = $removed_entity->name ?? $removed_entity->login_name ?? 'id '.$foreign_id;
                    $attribute .= ' removed';
                    $changed_attributes->push($attribute);
                }
            }
        }

        if (isset($changed_attributes) && $changed_attributes->isNotEmpty()) {
            $user = Auth::user();
            \App\GuiLog::log_changes([
                'user_id' => $user ? $user->id : 0,
                'username' 	=> $user ? $user->first_name.' '.$user->last_name : 'cronjob',
                'method' 	=> 'updated N:M',
                'model' 	=> Str::singular(Str::studly($obj->table)),
                'model_id'  => $obj->id,
                'text'		=> $changed_attributes->implode("\n"),
            ]);
        }

        return isset($changed_attributes) ? $changed_attributes : collect();
    }

    /**
     * Removes a specified model object from storage
     *
     * @param  int  $id:  bulk delete if == 0
     * @return Response
     */
    public function destroy($id)
    {
        // helper to inform the user about success on deletion
        $to_delete = 0;
        $deleted = 0;
        // bulk delete
        if ($id == 0) {
            $obj = static::get_model_obj();

            // Error Message when no Model is specified - NOTE: delete_message must be an array of the structure below !
            if (! Request::get('ids')) {
                $message = trans('messages.base.delete.noEntry');
                $obj->addAboveMessage($message, 'error');

                return Redirect::back()->with('delete_message', ['message' => $message, 'class' => NamespaceController::get_route_name(), 'color' => 'danger']);
            }

            foreach (Request::get('ids') as $id => $val) {
                $obj = $obj->findOrFail($id);
                $to_delete++;

                /* detach all pivot entries if many-to-many relations exist
                 * Note: This should be implemented as soft detach, but this functionality is actually not implemented by laravel
                 * So we could do it by ourself (DB::update(...)) but as many pivot tables do actually not have deleted_at timestamp
                 * we can preliminary just keep them in DB
                 */
                // foreach ($this->many_to_many as $rel) {
                // 	$func = explode('_', $rel)[0];
                // 	$obj->$func()->detach();
                // }

                if ($obj->delete()) {
                    $deleted++;
                }
            }
        } else {
            $to_delete++;
            $obj = static::get_model_obj();
            if ($obj->findOrFail($id)->delete()) {
                $deleted++;
            }
        }
        $obj = isset($obj) ? $obj : static::get_model_obj();
        $class = NamespaceController::get_route_name();
        $translatedClass = trans("messages.{$class}") != "messages.{$class}" ?: trans_choice("view.Header_{$class}", $deleted ?: 1);

        if (! $deleted && ! $obj->force_delete) {
            $color = 'danger';
            $message = trans('messages.base.delete.fail', ['model' => $translatedClass, 'id' => '']);
            $obj->addAboveMessage($message, 'error');
        } elseif (($deleted == $to_delete) || $obj->force_delete) {
            $color = 'success';
            $message = trans('messages.base.delete.success', ['model' => $translatedClass, 'id' => '']);
            $obj->addAboveMessage($message, 'success');
        } else {
            $color = 'warning';
            $message = trans('messages.base.delete.multiSuccess', ['deleted' => $deleted, 'to_delete' => $to_delete, 'model' => $translatedClass]);
            $obj->addAboveMessage($message, 'warning');
        }

        return Redirect::back()->with('delete_message', ['message' => $message, 'class' => $class, 'color' => $color]);
    }

    /**
     * API equivalent of destroy()
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_destroy($ver, $id)
    {
        if ($ver === '0') {
            $obj = static::get_model_obj();

            return response()->v0ApiReply([], $obj->findOrFail($id)->delete());
        } elseif ($ver === '1') {
            $service = new Service(new Repository(static::get_model_obj()));
            $data = $service->delete($id);

            return $this->response([]);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    /**
     * Detach a pivot entry of an n-m relationship
     *
     * @param 	id 			Integer 	Model ID the relational model is attached to
     * @param 	function 	String 		Function Name of the N-M Relation
     * @return Response Object 		Redirect back
     *
     * @author Nino Ryschawy
     */
    public function detach($id, $function)
    {
        $model = NamespaceController::get_model_name();
        $model = $model::find($id);

        if (\Request::has('ids')) {
            $model->{$function}()->detach(array_keys(\Request::get('ids')));
        }

        return \Redirect::back();
    }

    /**
     * API equivalent of the edit view
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_get($ver, $id)
    {
        if ($ver === '0') {
            $obj = static::get_model_obj()->findOrFail($id);

            return response()->v0ApiReply(['models' => [$id => $obj]], true, $id);
        } elseif ($ver === '1') {
            $resourceOptions = $this->parseResourceOptions();
            $service = new Service(new Repository(static::get_model_obj()));
            $data = $service->getById($id, $resourceOptions);
            $parsedData = $this->parseData($data, $resourceOptions);

            return $this->response($parsedData);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    /**
     * Get status of object via API
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_status($ver, $id)
    {
        if ($ver === '0') {
            // Throw ModelNotFoundException if not found, don't return success irrespectively
            static::get_model_obj()->findOrFail($id);

            return response()->v0ApiReply([], true, $id);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    /**
     * API equivalent of index()
     *
     * @author Ole Ernst
     *
     * @return JsonResponse
     */
    public function api_index($ver)
    {
        if ($ver === '0') {
            $query = static::get_model_obj();
            foreach (Request::all() as $key => $val) {
                $query = $query->where($key, $val);
            }

            return response()->v0ApiReply(['models' => $query->get()->keyBy('id')], true);
        } elseif ($ver === '1') {
            $resourceOptions = $this->parseResourceOptions();
            $service = new Service(new Repository(static::get_model_obj()));
            $data = $service->getAll($resourceOptions);
            $parsedData = $this->parseData($data, $resourceOptions);

            return $this->response($parsedData);
        } else {
            return response()->v0ApiReply(['messages' => ['errors' => ["Version $ver not supported"]]]);
        }
    }

    // Deprecated:
    protected $output_format;

    /**
     *  json abstraction layer
     */
    protected function json()
    {
        $this->output_format = 'json';

        $data = Request::all();
        $id = $data['id'];
        $func = $data['function'];

        if ($func == 'update') {
            return $this->update($id);
        }
    }

    /**
     *  Maybe a generic redirect is an option, but
     *  howto handle fails etc. ?
     */
    protected function generic_return($view, $param = null)
    {
        if ($this->output_format == 'json') {
            return $param;
        }

        if (isset($param)) {
            return Redirect::route($view, $param);
        } else {
            return Redirect::route($view);
        }
    }

    /**
     * Tree View Specific Stuff
     *
     * TODO: Implement the Tree View as Javascript Tree Table - preparations are already made in comments (use jstree.min.js)
     * 		 see Color Admin Bootstrap Theme: http://wrapbootstrap.com/preview/WB0N89JMK -> UI-Elements -> Tree View
     *
     * @author Nino Ryschawy
     *
     * global Variables
     *	$INDEX  : used for shifting the children elements
     *	$I 		: used to increment over specficied colours (defined in variable)
     */
    public static $INDEX = 0;
    public static $I = 0;
    public static $colours = ['', 'text-danger', 'text-success', 'text-warning', 'text-info'];

    /**
     * Returns the Tree View (Table) as HTML Text
     *
     * IMPORTANT NOTES
     * If the Model uses the Generic BaseController@index function a separate index.blade.php has to be installed in
     *	modules/Resources/Modelname/ that includes the Generic.tree blade
     * The Generic.tree blade calls this function
     * The Model currently has to have a function called get_tree_list that shall return the ordered tree of objects
     *	(with delete_disabled) - see NetElementType.php
     */
    public static function make_tree_table()
    {
        $data = '';

        // tree with select fields
        // $data .= '<div id="jstree-checkable" class="jstree jstree-2 jstree-default jstree-checkbox-selection" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false" aria-selected="false">';
        // $data .= '<ul class="jstree-container-ul jstree-children jstree-wholerow-ul jstree-no-dots" role="group">';

        // default tree
        // $data = '<div id="jstree-default" class="jstree jstree-1 jstree-default" role="tree" aria-multiselectable="true" tabindex="0" aria-activedescendant="j1" aria-busy="false">';
        // $data .= '<ul class="jstree-children" role="group" style>';

        $model = NamespaceController::get_model_name();
        $data .= self::_create_index_view_data($model::get_tree_list());

        // $data .= '</ul></div></div>';

        return $data;
    }

    /**
     * writes whole index view table data as HTML in string
     *
     * @param array with all Model Objects in hierarchical tree structure
     *
     * @author Nino Ryschawy
     */
    private static function _create_index_view_data($ordered_tree)
    {
        $data = '';

        foreach ($ordered_tree as $object) {
            // foreach ($ordered_tree as $key => $object)
            if (is_array($object)) {
                self::$INDEX += 1;
                if (self::$INDEX == 1) {
                    self::$I--;
                }

                // $data .= '<ul role="group" class="jstree-children" style>';
                $data .= self::_create_index_view_data($object);
            // $data .= '</ul>';
            } else {
                // $data .= self::_print_label_elem($object, isset($ordered_tree[$key+1]));
                $data .= self::_print_label_elem($object);
            }

            if (self::$INDEX == 0) {
                self::$I++;
            }
        }

        self::$INDEX -= 1;
        $data .= (self::$INDEX == 0) && (strpos(substr($data, strlen($data) - 8), '<br><br>') === false) ? '<br>' : '';

        return $data;
    }

    /**
     * Returns the HTML string for one label Element for Tree Index View
     *
     * @param $object 	Model Object
     *
     * @author Nino Ryschawy
     *
     * TODO: implement with jstree.min.js
     */
    // public static function _print_label_elem($object, $list = false)
    private static function _print_label_elem($object)
    {
        $cur_model_complete = get_class($object);
        $cur_model_parts = explode('\\', $cur_model_complete);
        $cur_model = array_pop($cur_model_parts);

        $data = '';

        // default tree
        // $data .= '<li role="treeitem" data-jstree="{&quot;opened&quot;:true, &quot;selected&quot;:true" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" aria-expanded="true" id="j'.self::$I.'" class="jstree-node jstree-open">';
        // 	$data .= $list ? '<i class="jstree-icon jstree-ocl" role="presentation"></i>' : '';

        // tree with select fields
        // $data .= '<li role="treeitem" aria-selected="false" aria-level="'.self::$INDEX.'" aria-labelledby="'.self::$I.'_anchor" id="j'.self::$I.'" class="jstree-node  jstree-leaf">';
        // 	$data .= '<div unselectable="on" role="presentation" class="jstree-wholerow">&nbsp;</div><i class="jstree-icon jstree-ocl" role="presentation"></i>';

        for ($cnt = 0; $cnt <= self::$INDEX; $cnt++) {
            $data .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        $data .= \Form::checkbox('ids['.$object->id.']', 1, null, null, ['style' => 'simple', 'disabled' => $object->index_delete_disabled ? 'disabled' : null]).'&nbsp;&nbsp;';
        // $data .= \HTML::linkRoute($cur_model.'.edit', $object->view_index_label(), $object->id, ['class' => self::$colours[self::$I % count(self::$colours)]]);
        $name = self::$INDEX == 0 ? '<strong>'.$object->view_index_label().'</strong>' : $object->view_index_label();
        $data .= '<a class="'.self::$colours[self::$I % count(self::$colours)].'" href="'.route($cur_model.'.edit', $object->id).'">'.$name.'</a>';
        $data .= '<br>';
        // link for javascript tree
        // $data .= '<a class="jstree-anchor" href="'.route($cur_model.'.edit', $object->view_index_label(), $object->id).'" tabindex="-1" id="'.self::$I.'_anchor">';
        // $data .= '<i class="jstree-icon jstree-themeicon fa fa-folder text-warning fa-lg jstree-themeicon-custom" role="presentation"></i>';
        // $data .= $object->view_index_label().'</a>';
        // $data .= '</li>';

        return $data;
    }

    /**
     * Return a List of Filenames (e.g. PDF Logos, Tex Templates)
     *
     * @param 	dir 	Directory name relative to storage/app/config/
     * @return array of Filenames
     *
     * @author 	Nino Ryschawy
     */
    public static function get_storage_file_list($dir)
    {
        $files[null] = 'None';
        foreach (\Storage::files("config/$dir") as $file) {
            $name = basename($file);
            $files[$name] = $name;
        }

        return $files;
    }

    /**
     * Grep Log entries of all severity Levels above the specified one of a specific Logfile
     *
     * @author Nino Ryschawy
     *
     * @return array Last Log entry first
     */
    public static function get_logs($filename, $severity_level = Logger::DEBUG)
    {
        $levels = Logger::getLevels();

        foreach ($levels as $key => $value) {
            if ($severity_level <= $value) {
                break;
            }

            unset($levels[$key]);
        }

        $levels = implode('\|', array_keys($levels));
        $filename = $filename[0] == '/' ? $filename : storage_path("logs/$filename");

        exec("grep \"$levels\" $filename", $logs);

        return array_reverse($logs);
    }

    /**
     * Process datatables ajax request.
     *
     * For Performance tests and fast Copy and Paste: $start = microtime(true) and $end = microtime(true);
     * calls view_index_label() which determines how datatables are configured
     * you can find examples in every model with index page
     * Documentation is written here, because this seems like the first place to look for it
     *
     * @param table - tablename of model
     * @param index_header - array like [$table.'.column1' , $table.'.column2', ..., 'foreigntable1.column1', 'foreigntable2.column1', ..., 'customcolumn']
     * order in index_header is important and datatables will have the column order given here
     * @param bsclass - defines a Bootstrap class for colering the Rows
     * @param header - defines whats written in Breadcrumbs Header at Edit and Create Pages
     * @param edit - array like [$table.'.column1' => 'customfunction', 'foreigntable.column' => 'customfunction', 'customcolumn' => 'customfunction']
     * customfunction will be called for every element in table.column, foreigntable.column or customcolumn
     * CAREFUL customcolumn will not be sortable or searchable - to use them anyways use the disable_sortsearch key
     * @param eager_loading array like [foreigntable1, foreigntable2, ...] - eager load foreign tables
     * @param order_by array like ['0' => 'asc'] - order table by id in ascending order, ['1' => 'desc'] - order table after first column in descending order
     * @param disable_sortsearch array like ['customcolumn' => 'false'] disables sorting & searching for the chosen column (e.g. when it is impossible) => prevent errors
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Christian Schramm
     *
     * NOTE: Further Datatables Documentation
     * 			https://datatables.yajrabox.com
     * 			https://yajrabox.com/docs/laravel-datatables/
     */
    public function index_datatables_ajax()
    {
        $model = static::get_model_obj();
        $dt_config = $model->view_index_label();
        $header_fields = $dt_config['index_header'];
        $edit_column_data = $dt_config['edit'] ?? [];
        $filter_column_data = $dt_config['filter'] ?? [];
        $eager_loading_tables = $dt_config['eager_loading'] ?? [];
        $raw_columns = $dt_config['raw_columns'] ?? []; // not run through htmlentities()

        $query = $model::select($dt_config['table'].'.*');

        if ($eager_loading_tables) {
            $query->with($eager_loading_tables);
        }

        if (isset($dt_config['scope'])) {
            $scope = $dt_config['scope'];
            $query->$scope();
        }

        $DT = DataTables::make($query)
            ->addColumn('checkbox', '');

        if (config('datatables.isIndexCachingEnabled')) {
            $count = $model->cachedIndexTableCount;
            if (! $count) {
                $count = $model::count();

                cache(['indexTables.'.$model->table => $count]);
            }

            $DT->setTotalRecords($count);
            // ->setFilteredRecords(10000)
            // ->skipTotalRecords()
        }

        // TODO: Just set this in where clause in query?
        foreach ($filter_column_data as $column => $custom_query) {
            // backward compatibility – accept strings as input, too
            if (is_string($custom_query)) {
                $custom_query = ['query' => $custom_query, 'eagers' => [], 'exact' => false];
            } elseif (! is_array($custom_query)) {
                throw new \Exception('$custom_query has to be string or array');
            }

            $DT->filterColumn($column, function ($query, $keyword) use ($custom_query) {
                if (isset($custom_query['exact']) && ! $custom_query['exact']) {
                    $keyword = "%{$keyword}%";
                }

                $query->with($custom_query['eagers'])->whereRaw($custom_query['query'], [$keyword]);
            });
        }

        $first_column = head($header_fields);
        if (Str::startsWith(head($header_fields), $dt_config['table'])) {
            $first_column = substr($first_column, strlen($dt_config['table']) + 1);
        }

        $DT->editColumn('checkbox', function ($model) {
            if (method_exists($model, 'set_index_delete')) {
                $model->set_index_delete();
            }

            return "<input style='simple' align='center' class='' name='ids[".$model->id."]' type='checkbox' value='1' ".
                ($model->index_delete_disabled ? 'disabled' : '').'>';
        })->editColumn($first_column, function ($model) use ($first_column) {
            $content = $model[$first_column];
            // Get cell content when data is eager loaded on first column
            if (strpos($first_column, '.') !== false) {
                $chain = explode('.', $first_column);

                $content = $model;
                foreach ($chain as $value) {
                    $content = $content->{$value};
                }
            }

            return '<a href="'.route(NamespaceController::get_route_name().'.edit', $model->id).'"><strong>'.
                $model->view_icon().$content.'</strong></a>';
        });

        foreach ($edit_column_data as $column => $functionname) {
            if ($column == $first_column) {
                $DT->editColumn($column, function ($model) use ($functionname) {
                    return '<a href="'.route(NamespaceController::get_route_name().'.edit', $model->id).
                        '"><strong>'.$model->view_icon().$model->$functionname().'</strong></a>';
                });
            } else {
                $DT->editColumn($column, function ($model) use ($functionname) {
                    return $model->$functionname();
                });
            }
        }

        $DT->setRowClass(function ($model) {
            if (method_exists($model, 'get_bsclass')) {
                return $model->get_bsclass();
            }

            return $model->view_index_label()['bsclass'] ?? 'info';
        });

        array_unshift($raw_columns, 'checkbox', $first_column); // add everywhere used raw columns

        return $DT->rawColumns($raw_columns)->make();
    }

    /**
     * Process autocomplete ajax request
     *
     * @return array
     *
     * @author Ole Ernst
     */
    public function autocomplete_ajax($column)
    {
        $model = static::get_model_obj();

        return $model->select($column)
            ->where($column, 'like', '%'.\Request::get('q').'%')
            ->distinct()
            ->pluck($column);
    }

    /**
     * Returns the Relations in a prepared format that is working with select2.
     * The Data must follow a specific structure for that. Hence following it
     * is explained how to use the "generic" of this function.
     *
     * 1. Add an options key to the view_form_fields array of the select field
     * a.) set key class to 'select2-ajax'
     * b.) set key route to {Modelname}.select2 and set the route parameter
     *    relation to the plural name of the relation
     *
     * 2. Add a public method inside the model which conforms to the naming
     *   schema: "select2{Relation}"
     * a.) This function should return an Eloquent Builder instance
     * b.) The query should have selected 'id' and the label for each element as
     *     'text'
     * c.) The query MUST contain a when($search, function ($query, $search)) to
     *     handle the seach inside the select field
     * d.) optional count can be defined to have a count displayed to every
     *    option of the select field
     *
     * example implementation can be found in Modem(Controller)
     *
     * @param  string  $relation
     * @return \Illuminate\Pagination\Paginator
     */
    public function select2Ajax(string $relation)
    {
        $search = request('search');
        $relation = ucfirst($relation);
        $model = static::get_model_obj();

        if (! method_exists($model, "select2{$relation}")) {
            throw new \BadMethodCallException("select2{$relation} does not exist");
        }

        $selectQuery = $model->{"select2{$relation}"}($search);

        if (! $selectQuery instanceof \Illuminate\Database\Eloquent\Builder) {
            throw new \UnexpectedValueException("Return type of select2{$relation} should be \Illuminate\Database\Eloquent\Builder.");
        }

        return $selectQuery->paginate(20);
    }

    /**
     * Prepare the preselected model for the select 2 field. Currently only
     * single select is supported.
     *
     * @param  BaseModel|null  $model  model in edit view, null in create context
     * @param  string  $class  unqualified name of the Class
     * @param  string|null  $field  Name of the input field
     * @param  string|null  $fn  Name of the relation(function)
     * @return array
     */
    protected function setupSelect2Field($model, string $class, string $field = null, string $fn = null): array
    {
        $lowerField = strtolower($class);
        $field = $field ?? "{$lowerField}_id";
        $placeholder = trans('view.select.base', ['model' => trans("view.select.{$class}")]);
        $isSetViaRequest = request($field) && array_key_exists($class, $models = session('models', BaseModel::get_models()));

        if ($model->exists) {
            $fn = $fn ?? $lowerField;

            if ($isSetViaRequest) {
                return $this->select2ViaRequestParam($models, $model, $class, $field, $placeholder);
            }

            return [
                null => $placeholder,
                optional($model->$fn)->id => optional($model->$fn)->label(),
            ];
        }

        if ($isSetViaRequest) {
            return $this->select2ViaRequestParam($models, $model, $class, $field, $placeholder);
        }

        return [null => $placeholder];
    }

    /**
     * Set the Select 2 Key/Value via Request (GET) Parameter
     *
     * @param  array  $models
     * @param  BaseModel  $model
     * @param  string  $class
     * @param  string  $field
     * @param  string  $placeholder
     * @return array
     */
    protected function select2ViaRequestParam(array $models, BaseModel $model, string $class, string $field, string $placeholder): array
    {
        $select = [null => $placeholder];

        if ($model = $models[$class]::find(request($field))) {
            $select[$model->id] = $model->label();
        }

        return $select;
    }

    /**
     * This creates the AJAX data for the DataTables inside the relation panels
     * of the edit views.
     * Prerequisits:
     * 1. The related model MUST have a public label() method
     * 2. The view_has_many array MUST contain a count value (this way the ajax
     *    handling is triggered)
     *
     * for example implementations look into Contract.
     *
     * @param  int  $model  id of the model
     * @param  string  $relationClass  unqualified class name of the relation
     * @return void
     */
    public function getRelationDatatable($model, $relationClass)
    {
        $model = static::get_model_obj()->findOrFail($model);
        $relationFn = \Illuminate\Support\Str::plural(strtolower($relationClass));
        $order = $relationClass !== 'Invoice' ? 'asc' : 'desc';

        return datatables($model->$relationFn()->orderBy('id', $order))
            ->addColumn('checkbox', function ($model) {
                if (method_exists($model, 'set_index_delete')) {
                    $model->set_index_delete();
                }

                return "<input style='simple' align='center' class='' name='ids[".$model->id."]' type='checkbox' value='1' ".
                ($model->index_delete_disabled ? 'disabled' : '').'>';
            }, 0)
            ->addColumn('label', function ($model) use ($relationClass) {
                return '<a href="'.route($relationClass.'.edit', $model->id).'">'.
                    $model->view_icon().' '.$model->label().'</a>';
            }, 1)
            ->only(['checkbox', 'label'])
            ->rawColumns(['checkbox', 'label'])
            ->setRowClass(function ($model) {
                if (method_exists($model, 'get_bsclass')) {
                    return $model->get_bsclass();
                }

                return $model->view_index_label()['bsclass'] ?? 'info';
            })
            ->make();
    }

    /**
     *  The official Documentation Help Menu Function
     *
     *  See: See: config/documenation.php array
     *
     *  @author Torsten Schmidt
     *
     *  @return array of ['doc' => link, 'youtube' => link, 'url' => 'link']
     */
    public function view_help()
    {
        // helper to get model name from controller context
        $a = explode('\\', strtolower(NamespaceController::get_model_name()));

        return config('documentation.'.strtolower(end($a)));
    }

    /**
     * Show error message when user clicks on analysis page and ProvMon module is not installed/active
     *
     * @author Nino Ryschawy
     *
     * @return View
     */
    public function missingModule($module)
    {
        $error = '501';
        $message = trans('messages.missingModule', ['module' => $module]);

        return \View::make('errors.generic', compact('error', 'message'));
    }

    /**
     * create thumbnail of quick view network for navbar
     *
     * @author Farshid Ghiasimanesh
     *
     * @return array of alerts are related to target netelement
     */
    public function alarmsNetElement($netelement, $withThumbnail = false)
    {
        // Alarm::where('status', 'active')->whereDescendantsOf([user favorited MarketNetElement])->get()
        // count warning, critical etc.
        $result = [
            'title' => $netelement->name,
            'info' => 0,
            'warning' => 0,
            'critical' => 0,
        ];

        $alerts = json_decode(AlertmanagerApi::callApi("alerts?filter=host%3D$netelement->ip&silenced=false&inhibited=false&active=true", 'GET', 2), true);
        if ($alerts) {
            foreach ($alerts as $alert) {
                switch ($alert['labels']['severity']) {
                    case 'info':
                        $result['info'] += 1;
                        break;
                    case 'warning':
                        $result['warning'] += 1;
                        break;
                    case 'critical':
                        $result['critical'] += 1;
                        break;
                }
            }
        }

        $result['sum'] = array_sum([$result['info'], $result['warning'], $result['critical']]);

        if (! $withThumbnail) {
            return $result;
        }

        // create thumbnail
        $start = -90;

        $thumbnail = imagecreatetruecolor(200, 200);
        $transColor = imagecolortransparent($thumbnail, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
        imagefill($thumbnail, 0, 0, $transColor);

        if ($result['info']) {
            $temp = $result['info'] * 360 / $result['sum'];
            imagefilledarc($thumbnail, 100, 100, 200, 200, $start, $start + $temp - 2, imagecolorallocate($thumbnail, 0x0E, 0xA5, 0xE9), IMG_ARC_PIE);
            $start += abs($temp);
        }

        if ($result['warning']) {
            $temp = $result['warning'] * 360 / $result['sum'];
            imagefilledarc($thumbnail, 100, 100, 200, 200, $start + 2, $start + $temp - 2, imagecolorallocate($thumbnail, 0xEA, 0xB3, 0x08), IMG_ARC_PIE);
            $start += abs($temp);
        }

        if ($result['critical']) {
            $temp = $result['critical'] * 360 / $result['sum'];
            imagefilledarc($thumbnail, 100, 100, 200, 200, $start + 2, $start + $temp - 1, imagecolorallocate($thumbnail, 0xEF, 0x44, 0x44), IMG_ARC_PIE);
            $start += abs($temp);
        }

        if (! $result['sum']) {
            imagefilledarc($thumbnail, 100, 100, 200, 200, 0, 360, imagecolorallocate($thumbnail, 0x7F, 0xB4, 0x33), IMG_ARC_PIE);
        }

        // inner circle
        imagefilledarc($thumbnail, 100, 100, 100, 100, 0, 360, imagecolorallocate($thumbnail, 0xFF, 0xFF, 0xFF), IMG_ARC_PIE);

        if (! File::exists(public_path('storage/public'))) {
            File::makeDirectory(public_path('storage/public'), 0775, true);
        }

        imagewebp($thumbnail, public_path('storage/public/overview_network.webp'));
    }
}
