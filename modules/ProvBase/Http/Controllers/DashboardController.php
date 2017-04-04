<?php

namespace Modules\ProvBase\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Modules\BillingBase\Entities\Item;
use View;
use Illuminate\Support\Facades\App;

use Modules\ProvBase\Entities\Contract;
use Modules\BillingBase\Entities\Product;

class DashboardController extends BaseController
{
    public function index()
    {
        $title = 'Dashboard';
		$period = 'lastMonth';

        $days = null;
		$monthly_sales = null;
        $checked = '';
		$show_sales = false;

		$contracts = array();
		$chart_data_contracts = array();

        try {
			if (App::environment() !== 'production') {
				$show_sales = true;
			}

            // check if the dayfiltet form submitted
            $request = URL::getRequest();
            if ($request->isMethod('post')) {
                $days = $request->input('datefilter');
                $monthly_sales = $request->input('switch-sales');
            }

            // get all valid contracts
            $all_contracts = $this->get_contracts();

            // get last month contracts
			$filtered_contracts = $this->get_contracts_by_filter($all_contracts, $period);

			// get contracts by given last days
			if (!is_null($days)) {
				$period = 'dayPeriod';
				$filtered_contracts = $this->get_contracts_by_filter($all_contracts, $period, $days);
			}

			if (count($all_contracts) > 0) {

				$contracts = array(
					'count_all' => count($all_contracts),
					'count_filtered' => count($filtered_contracts),
					'period' => $period,
					'days' => $days
				);

				// get chart data: contracts
				$chart_data_contracts = $this->get_chart_data_contracts($all_contracts);
			}
        } catch (\Exception $e) {
            \Log::error('Dashboard-Exception: ' . $e->getMessage());
            throw $e;
        }

        return View::make(
            'provbase::dashboard', $this->compact_prep_view(
                compact('title', 'contracts', 'chart_data_contracts', 'show_sales', 'sales', 'chart_data_sales', 'checked')
            )
        );
    }

	/**
	 * Get all valid contracts
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private function get_contracts()
	{
		$ret = array();

		try {
			// find contracts with related items and products
			$contracts = Contract::orderBy('contract_start', 'asc')->with('items', 'items.product')->get();

			if (count($contracts) > 0) {
				foreach ($contracts as $contract) {

					// check start- and enddate
					if ($contract->contract_start <= $this->generate_reference_date() &&
						($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d') || is_null($contract->contract_end))) {

						$ret[] = $contract;
					}
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}
		return $ret;
	}

	/**
	 * Filter contracts by period
	 *
	 * @param array $contracts
	 * @param string $period
	 * @param integer $days
	 * @return array
	 * @throws \Exception
	 */
	private function get_contracts_by_filter(array $contracts, $period, $days = null)
	{
		$ret = array();

		try {
			$date = $this->generate_reference_date($period, $days);

			if (count($contracts) > 0) {
				foreach ($contracts as $key => $contract) {
					if ($contract->contract_start <= $date &&
						($contract->contract_end == '0000-00-00' || $contract->contract_end > $date || is_null($contract->contract_end))) {
							$ret[] = $contract;
					}
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return $ret;
	}

	/**
	 * Generate date by given period
	 *
	 * @param string $period
	 * @param integer $days
	 * @return false|string
	 * @throws \Exception
	 */
	private function generate_reference_date($period = null, $days = null)
	{
		$ret = date('Y-m-d');

		try {
			$month = date('m');
			$year = date('Y');

			if (!is_null($period)) {
				switch ($period) {
					case 'lastMonth':
						$month = $month - 1;
						if (($month) == 0) {
							$month = 12;
							$year = $year - 1;
						}

						if (strlen($month) == 1) {
							$month = '0' . $month;
						}
						$ret = $year . '-' . $month . '-' . date('t', mktime(0, 0, 0, $month, 1, $year));
						break;

				case 'dayPeriod':
					$date = date_create($ret);
					date_sub($date, date_interval_create_from_date_string($days . ' days'));
					$ret = date_format($date, 'Y-m-d');
					break;
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return $ret;
	}

    /**
     * Returns data for the line chart
     *
	 * @param array $contracts
     * @return array
     * @throws \Exception
     */
    private function get_chart_data_contracts(array $contracts)
    {
        $j = 2;
        $ret = array();

        try {
			$date = date_create(date('Y-m-d'));
			$date = date_format(date_sub($date, date_interval_create_from_date_string(12 . ' months')), 'Y-m-d');
			$date_parts = explode('-', $date);

			$year = $date_parts[0];
			for ($i = 0; $i <= 12; $i++) {
				if ($date_parts[1] + $i == 13) {
					$year = $year + 1;
					$month = 1;
				} elseif ($date_parts[1] + $i > 13) {
					$month = $j++;
				} else {
					$month = $date_parts[1] + $i;
				}

				$contract_start = $year . '-' . str_pad($month, 2 ,'0', STR_PAD_LEFT) . '-01';
				$contract_end = $year . '-' . str_pad($month, 2 ,'0', STR_PAD_LEFT) . '-' . date("t", mktime(0, 0, 0, $month, 1, $year));

				$ret['labels'][] = str_pad($month, 2 ,'0', STR_PAD_LEFT) . '/' . $year;
				$ret['contracts'][] = $this->count_contracts($contracts, $contract_start, $contract_end);
			}
		} catch (\Exception $e) {
        	throw $e;
		}
        return $ret;
    }

	/**
	 * Count contracts for given time interval
	 *
	 * @param array $contracts
	 * @param string $contract_start
	 * @param string $contract_end
	 * @return int
	 * @throws \Exception
	 */
    private function count_contracts(array $contracts, $contract_start, $contract_end)
	{
		$ret = 0;

		try {
			foreach ($contracts as $contract) {
				if (($contract->contract_start >= $contract_start && $contract->contract_end <= $contract_end) &&
					($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d') || is_null($contract->contract_end))) {

					$ret++;
				}
			}
		} catch (\Exception $e) {
			throw $e;
		}

		return count($contracts) - $ret;
	}
}