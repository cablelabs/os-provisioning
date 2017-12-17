<?php

namespace App\Http\Controllers;

use App\GuiLog;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class GuiLogController extends BaseController {

	protected $index_create_allowed = false;
	protected $index_delete_allowed = false;
	protected $edit_view_save_button = false;


    /**
     * defines the formular fields for the edit and create view
     */
	public function view_form_fields($model = null)
	{
		$a = array(
			array('form_type' => 'text', 'name' => 'username', 'description' => 'Username'),
			array('form_type' => 'text', 'name' => 'method', 'description' => 'Method'),
			array('form_type' => 'text', 'name' => 'model', 'description' => 'Model'),
			array('form_type' => 'text', 'name' => 'model_id', 'description' => 'ID'),
			array('form_type' => 'textarea', 'name' => 'text', 'description' => 'Changed Attributes'),
			);

		// add link of changed Model in edit view - Note: check if route exists is necessary because CccAuthuser.edit is not available for instance
		if ($model && \Route::getRoutes()->hasNamedRoute($model->model.'.edit'))
		{
			array_push($a, array('form_type' => 'text', 'name' => 'link', 'description' => 'Link', 'html' =>
				'<div class="col-md-12" style="background-color:white">
					<div class="form-group row"><label style="margin-top: 10px;" class="col-md-4 control-label">Link</label>
						<div class="col-md-7">
							<a class="btn btn-default btn-block" href="'.route($model->model.'.edit', ['id' => $model->model_id]).'"> '.$model->model.' '.$model->model_id.'</a>
						</div>
					</div>
				</div>'
				));
		}

		return $a;
	}

	public function filter(Request $request)
	{
		try {
			$params = $request->all();
			$create_allowed = $this->index_create_allowed;
			$delete_allowed = $this->index_delete_allowed;

			$model = $params['model'];
			$model_id = $params['model_id'];

			$request_query = GuiLog::where('model', '=', $model)
			->where('model_id', '=', $model_id)
			->orderBy('id', 'desc')
			->get();

			$DT = Datatables::of($request_query);
			$DT ->addColumn('responsive', '')
				->setRowClass('info')
				->editColumn('created_at', function($object) {
				return '<a href="'.route(\NamespaceController::get_route_name().'.edit', $object->id).'"><strong>'.
				$object->view_icon().$object->created_at.'</strong></a>';
			});

			return $DT->make(true);
		} catch (\Exception $e) {
			throw $e;
		}
	}
}
