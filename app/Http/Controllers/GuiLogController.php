<?php

namespace App\Http\Controllers;

use App\GuiLog;
use Illuminate\Http\Request;
use View;

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
			$model = $params['model'];
			$model_id = $params['model_id'];

			$view_var = GuiLog::where('model', '=', $model)
				->where('model_id', '=', $model_id)
				->orderBy('id', 'desc')
				->get();

			$create_allowed = $this->index_create_allowed;
			$delete_allowed = $this->index_delete_allowed;

			$model = new GuiLog();
			$view_header = \App\Http\Controllers\BaseViewController::translate_view('Overview','Header');
			$headline = \App\Http\Controllers\BaseViewController::translate_view( $model->view_headline(), 'Header' , 2 );
			$b_text	= $model->view_headline();

			return View::make ('Generic.index', $this->compact_prep_view(compact('headline','view_header', 'model', 'view_var', 'create_allowed', 'delete_allowed', 'b_text')));
		} catch (\Exception $e) {
			throw $e;
		}
	}
}
