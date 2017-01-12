<?php

namespace Modules\ProvBase\Http\Controllers;

//use Pingpong\Modules\Routing\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use View;

use Modules\ProvBase\Entities\Contract;

class DashboardController extends BaseController
{
    public function index()
    {
        $title = 'Dashboard';

        try {
            // test
            $this->get_contracts();

            // get all valid contracts
            $contracts = $this->get_valid_contracts();
            $contract_count = count($contracts);

            // get all valid contract of the current month
            list($contracts_current_month, $name_of_current_month) = $this->get_current_month_contracts($contracts);
            $contract_count_current_month = count($contracts_current_month);

            // get all contracts itemised by product types
            $itemised_contracts = $this->get_itemised_contracts($contracts);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        $contracts_all = count(Contract::all());
        return View::make(
            'provbase::dashboard', $this->compact_prep_view(
                compact('title', 'contract_count', 'contract_count_current_month', 'name_of_current_month', 'contracts',
                    'contracts_all'
                )
            )
        );
    }

    private function get_contracts()
    {
        $all_contracts = 0;
        $all_valid_contracts = 0;
        $all_contracts_current_month = 0;
        $all_valid_contracts_current_month = 0;

        try {
//            $all_contracts =  count($this->get_all_contracts(false));
//            $all_valid_contracts = count($this->get_all_contracts(true));
//            $all_contracts_current_month = count($this->get_all_contracts(false, date('Y-m-d')));
            $all_valid_contracts_current_month = count($this->get_all_contracts(true, date('Y-m-d')));

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
d($all_contracts, $all_valid_contracts, $all_contracts_current_month, $all_valid_contracts_current_month);
    }

    /**
     *
     * Refactoring: Guggst du besser in accountingCommand ab Zeile 117
     *
     * @param $product_types
     * @throws \Exception
     */
    private function get_all_contracts($check_validity, $date = null)
    {
        $ret_val = array();

        try {
            if (!is_null($date)) {
                $contract_start = date('Y-m-' . '01');
                $contract_end = $date;

                $contracts = DB::table('contract')->whereBetween('contract_start', array($contract_start, $contract_end))->get();

                if (count($contracts) > 0) {
                    foreach ($contracts as $contract) {
                        $contracts[] = Contract::find($contract['id']);
                    }
                }
            } else {
                $contracts = Contract::all();
            }

            if ($check_validity === true) {
                foreach ($contracts as $key => $contract) {
d($contract);
                    if ($contract->check_validity('Now')) {
                        $ret_val[] = $contract;
                    }
                }
            } else {
                $ret_val = $contracts;
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $ret_val;
    }

    private function get_current_month_contracts($contracts)
    {
        $filtered_contracts = array();
        $current_month = date('Y-m');
        $current_name_of_month = $this->get_name_of_month(date('n'));

        try {
            foreach ($contracts as $contract) {
                $contract_start_month = date('Y-m', strtotime($contract->contract_start));

                if ($current_month === $contract_start_month) {
                    $filtered_contracts[] = $contract;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return array($filtered_contracts, $current_name_of_month);
    }

    private function get_name_of_month($month)
    {
        $months = array(
            '1' => 'Januar', '2' => 'Februar', '3' => 'März', '4' => 'April',
            '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August',
            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember',
        );

        return $months[$month];
    }

    private function get_itemised_contracts($contracts)
    {
        $ret_val = array();
        $product_types = array(
            'Internet', 'TV', 'Voip', 'Device', 'Credit', 'Other'
        );

        try {
            foreach ($contracts as $contract) {

                foreach ($product_types as $key => $product_type) {
                    if (!array_key_exists($product_type, $ret_val)) {
                        $ret_val[$product_type] = 0;
                    }

                    $tariff = $contract->get_valid_tariff_count($product_type);

                    if (!is_null($tariff) && $tariff != 0) {
                        $ret_val[$product_type] = $ret_val[$product_type] + $tariff;
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $ret_val;
    }
}