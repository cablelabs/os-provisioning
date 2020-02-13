<?php

namespace App\Http\Controllers;

use Str;
use App\GuiLog;
use App\BaseModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GuiLogController extends BaseController
{
    protected $index_create_allowed = false;
    protected $index_delete_allowed = false;
    protected $edit_view_save_button = false;

    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $models = BaseModel::get_models();
        $cannotRestore = ['Invoice', 'SettlementRun'];
        $restorable = ! in_array($model->model, $cannotRestore);
        $isForceDeleteDisabled = ! $model->getDefaultProperty($models[$model->model], 'forceDeleting');
        $isModelTrashed = $isForceDeleteDisabled ?
            $models[$model->model]::withTrashed()->find($model->model_id)->trashed() :
            false;

        $fields = [
            ['form_type' => 'text', 'name' => 'username', 'description' => 'Username'],
            ['form_type' => 'text', 'name' => 'method', 'description' => 'Method'],
            ['form_type' => 'text', 'name' => 'model', 'description' => 'Model'],
            ['form_type' => 'text', 'name' => 'model_id', 'description' => 'ID'],
            ['form_type' => 'textarea', 'name' => 'text', 'description' => 'Changed Attributes'],
        ];

        // add link of changed Model in edit view - Note: check if route exists is necessary because CccUser.edit is
        // not available for instance
        if ($models && \Route::getRoutes()->hasNamedRoute($model->model.'.edit') && ! $isModelTrashed) {
            $route = route($model->model.'.edit', ['id' => $model->model_id]);

            $fields[] = [
                'form_type' => 'html',
                'name' => 'link',
                'description' => 'Link',
                'html' => '<div class="col-md-7">
                        <a class="btn btn-default btn-block" href="'.$route.'">'.$model->model.' '.$model->model_id.'</a>
                    </div>',
            ];
        }

        // addition in edit view to create link for restoring deleted models
        if ($isModelTrashed && $restorable) {
            $route = route('Guilog.restore', ['id' => $model->id]);

            $fields[] = [
                'form_type' => 'html',
                'name' => 'deleted_at',
                'description' => 'Restore',
                'html' => '<div class="col-md-7">
                        <a class="btn btn-default btn-block" href="'.$route.'"> Restore '.$model->model.'</a>
                    </div>',
            ];
        }

        return $fields;
    }

    /**
     * Restore a soft-deleted model
     *
     * @param id GuiLog
     *
     * @author Roy Schneider
     */
    public function restoreModel($id)
    {
        $modelArray = BaseModel::get_models();
        $guilog = GuiLog::find($id);
        $modelToRestore = $modelArray[$guilog->model]::withTrashed()->find($guilog->model_id);
        $modelToRestore->restore($guilog->model);

        if (\Route::has($guilog->model.'.edit')) {
            return redirect()->route($guilog->model.'.edit', ['id' => $guilog->model_id]);
        } else {
            return redirect()->route('GuiLog.index');
        }
    }

    public function filter($id, Request $request)
    {
        $uri = explode('/', $request->getRequestUri());
        $routeName = NamespaceController::get_route_name();
        $modelName = Str::lower($uri[count($uri) - 3]);

        $request_query = GuiLog::where('model', '=', $modelName)
            ->where('model_id', '=', $id)
            ->orderBy('id', 'desc')
            ->get();

        return DataTables::make($request_query)
            ->addColumn('responsive', '')
            ->setRowClass(function ($guilog) {
                $lookup = [
                    'created' => 'success',
                    'deleted' => 'danger',
                ];

                return $lookup[$guilog->method] ?? 'info';
            })
            ->editColumn('created_at', function ($guilog) use ($routeName) {
                return '<a href="'.route($routeName.'.edit', $guilog->id).
                        '" title="'.str_replace(', ', '1&#013;', $guilog->text).'"><strong>'.
                        $guilog->view_icon().$guilog->created_at.'</strong></a>';
            })
            ->rawColumns(['created_at'])
            ->make();
    }
}
