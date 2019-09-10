<?php

namespace Modules\Dunning\Http\Controllers;

use View;
use Yajra\Datatables\Datatables;
use Modules\Dunning\Entities\Debt;
use App\Http\Controllers\BaseViewController;

class DebtController extends \BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        if (! $model->date) {
            $model->date = date('Y-m-d');
        }

        if (! $model->cleared && (! $model->id || ! Debt::where('parent_id', $model->id)->count())) {
            $selectList[null] = null;
            $contract_id = $model->contract_id ?: \Request::get('contract_id');

            // Type 1 (sta): Bank file upload with whole history of debts
            // Type 2 (csv): Debt import from financial accounting software (no history, only still open debts)
            $debts = Debt::where('contract_id', $contract_id)->where('cleared', 0)
                ->whereNull('parent_id')
                ->where('id', '!=', $model->id)
                ->get();

            foreach ($debts as $debt) {
                $selectList[$debt->id] = $debt->label();
            }

            $fields1[] = ['form_type' => 'select', 'name' => 'parent_id', 'description' => 'Debt to clear', 'value' => $selectList];
        } else {
            $fields1[] = ['form_type' => 'text', 'name' => 'parent_id', 'description' => 'Debt to clear', 'hidden' => 1];
        }

        // label has to be the same like column in sql table
        $fields2 = [
            ['form_type' => 'text', 'name' => 'contract_id', 'description' => 'Contract', 'hidden' => 1],
            ['form_type' => 'text', 'name' => 'voucher_nr', 'description' => 'Voucher number'],
            ['form_type' => 'text', 'name' => 'number', 'description' => 'Payment number', 'space' => 1],
            ['form_type' => 'text', 'name' => 'amount', 'description' => 'Amount'],
            ['form_type' => 'text', 'name' => 'missing_amount', 'description' => 'Missing amount', 'hidden' => 'C', 'options' => ['readonly']],
            ['form_type' => 'text', 'name' => 'bank_fee', 'description' => 'Bank fee'],
            ['form_type' => 'text', 'name' => 'total_fee', 'description' => 'Total fee', 'help' => trans('dunning::help.debt.total_fee'), 'space' => 1],
            ['form_type' => 'text', 'name' => 'date', 'description' => 'Voucher date'],         // Belegdatum
            ['form_type' => 'text', 'name' => 'due_date', 'description' => 'RCD', 'space' => 1],

            ['form_type' => 'text', 'name' => 'indicator', 'description' => 'Dunning indicator'],
            ['form_type' => 'text', 'name' => 'dunning_date', 'description' => 'Dunning date', 'space' => 1],

            ['form_type' => 'checkbox', 'name' => 'cleared', 'description' => trans('dunning::view.cleared'), 'options' => ['onclick' => "return false;", 'readonly']],
            ['form_type' => 'textarea', 'name' => 'description', 'description' => 'Description'],
        ];

        return array_merge($fields1, $fields2);
    }

    public function prepare_input($data)
    {
        $data['indicator'] = $data['indicator'] ?? 0;
        $data['bank_fee'] = $data['bank_fee'] ?? 0;
        $data['total_fee'] = $data['total_fee'] ?? 0;

        return parent::prepare_input($data);
    }

    /**
     * Separate index page for the resulting outstanding payments of each customer
     *
     * Here the all the customers with a sum unequal zero of all amounts and total fees of his debts are shown
     *
     * @return View
     */
    public function result()
    {
        $model = static::get_model_obj();
        $headline = trans('dunning::view.debt.headline');
        $view_header = BaseViewController::translate_view('Overview', 'Header');
        $create_allowed = $delete_allowed = false;

        $view_path = 'Generic.index';
        $ajax_route_name = 'Debt.result.data';

        return View::make($view_path, $this->compact_prep_view(compact('headline', 'view_header', 'model', 'create_allowed', 'delete_allowed', 'ajax_route_name')));
    }

    /**
     * Adapted copy of the BaseController function
     *
     * Here the all amounts and total fees of the debts of a customer are sumed-up
     * and customers with sum of zero are excluded
     */
    public function result_datatables_ajax()
    {
        $model = new Debt;
        $dt_config = $model->view_index_label();

        $header_fields = $dt_config['index_header'];
        $edit_column_data = isset($dt_config['edit']) ? $dt_config['edit'] : [];
        $filter_column_data = isset($dt_config['filter']) ? $dt_config['filter'] : [];
        $eager_loading_tables = isset($dt_config['eager_loading']) ? $dt_config['eager_loading'] : [];
        $additional_raw_where_clauses = isset($dt_config['where_clauses']) ? $dt_config['where_clauses'] : [];

        // if no id Column is drawn, draw it to generate links with id
        ! array_has($header_fields, $dt_config['table'].'.id') ? array_push($header_fields, 'id') : null;

        $request_query = Debt::groupBy('contract_id')->selectRaw('debt.id, contract_id, date,
            (sum(amount) + sum(total_fee)) as sum,
            sum(amount) as amount,
            sum(total_fee) as total_fee')->having('sum', '!=', 0);

        if ($eager_loading_tables) {
            $request_query = $request_query->with($eager_loading_tables);
        }

        $first_column = head($header_fields);

        // apply additional where clauses
        foreach ($additional_raw_where_clauses as $where_clause) {
            $request_query = $request_query->whereRaw($where_clause);
        }

        $DT = Datatables::make($request_query);
        $DT->addColumn('responsive', '')
            ->addColumn('checkbox', '');

        foreach ($filter_column_data as $column => $custom_query) {
            $DT->filterColumn($column, function ($query, $keyword) use ($custom_query) {
                $query->whereRaw($custom_query, ["%{$keyword}%"]);
            });
        }

        $DT->editColumn('checkbox', function ($object) {
            if (method_exists($object, 'set_index_delete')) {
                $object->set_index_delete();
            }

            return "<input style='simple' align='center' class='' name='ids[".$object->id."]' type='checkbox' value='1' ".
                ($object->index_delete_disabled ? 'disabled' : '').'>';
        })->editColumn($first_column, function ($object) use ($first_column) {
            return '<a href="'.route('Contract.edit', $object->contract_id).'#Billing"><strong>'.array_get($object, $first_column).'</strong></a>';
        });

        foreach ($edit_column_data as $column => $functionname) {
            if ($column == $first_column) {
                continue;
            } else {
                $DT->editColumn($column, function ($object) use ($functionname) {
                    return $object->$functionname();
                });
            }
        }

        $DT->setRowClass(function ($object) {
            return $object->view_index_label()['bsclass'];
        });

        return $DT->rawColumns(['checkbox', $first_column])->make(true);
    }
}
