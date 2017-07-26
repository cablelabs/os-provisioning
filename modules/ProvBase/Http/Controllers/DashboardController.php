<?php

namespace Modules\ProvBase\Http\Controllers;

use View;
use Log;

use App\Http\Controllers\BaseController;
use Modules\ProvBase\Entities\Contract;

class DashboardController extends BaseController
{
	public function index()
	{
		$title = 'Dashboard';

		$contracts = array();
		$income = array();
		$chart_data_contracts = array();
		$chart_data_income = array();

		$allowed_to_see = array(
			'accounting' => false,
			'technican' => false
		);
		$allowed_roles = array(
			3 => 'technican',
			4 => 'accounting'
		);

		// check user permissions
		$roles = \Auth::user()->roles();
		foreach ($roles as $role)
		{
			// allow super-admin to see everything
			// if ($role->id == 1)
			// {
			// 	$allowed_to_see = ['accounting' => true, 'technican' => true];
			// 	break;
			// }

			if (array_key_exists($role->id, $allowed_roles))
				$allowed_to_see[$allowed_roles[$role->id]] = true;
		}

		// get all valid contracts
		$contracts = $this->get_contracts();

		if (count($contracts) > 0) {

			// get chart data: contracts
			$chart_data_contracts = $this->get_chart_data_contracts($contracts);

			// income - TODO: only calculate when user has permissions - or better calculate only during night and save to DB
			if (\PPModule::is_active('billingbase') && $allowed_to_see['accounting']) {
				$income = $this->get_income_total($contracts);
				$chart_data_income = $this->get_chart_data_income($income);
			}
		}


		return View::make('provbase::dashboard', $this->compact_prep_view(
				compact('title', 'contracts', 'chart_data_contracts', 'income', 'chart_data_income', 'allowed_to_see')
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

		if (\PPModule::is_active('billingbase')) {
			// find contracts with related items and products
			$contracts = Contract::orderBy('contract_start', 'asc')->with('items', 'items.product')->get();
		} else {
			$contracts = Contract::orderBy('contract_start', 'asc')->get();
		}

		if (count($contracts) > 0) {
			$date = $this->generate_reference_date();

			foreach ($contracts as $contract) {

				// check start- and enddate
				if ($contract->contract_start <= $date &&
					($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d') || is_null($contract->contract_end))) {

					$ret[] = $contract;
				}
			}
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

		$date = $this->generate_reference_date($period, $days);

		if (count($contracts) > 0) {
			foreach ($contracts as $key => $contract) {
				if ($contract->contract_start <= $date &&
					($contract->contract_end == '0000-00-00' || $contract->contract_end > $date || is_null($contract->contract_end))) {
						$ret[] = $contract;
				}
			}
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

		return $ret;
	}

	/**
	 * Returns rehashed data for the line chart
	 *
	 * @param array $contracts
	 * @return array
	 * @throws \Exception
	 */
	private function get_chart_data_contracts(array $contracts)
	{
		$j = 2;
		$ret = array();

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

			$date_interval_start = $year . '-' . str_pad($month, 2 ,'0', STR_PAD_LEFT) . '-01';
			$date_interval_end = $year . '-' . str_pad($month, 2 ,'0', STR_PAD_LEFT) . '-' . date("t", mktime(0, 0, 0, $month, 1, $year));

			$ret['labels'][] = str_pad($month, 2 ,'0', STR_PAD_LEFT) . '/' . $year;
			$ret['contracts'][] = $this->count_contracts($contracts, $date_interval_start);
		}

		return $ret;
	}

	/**
	 * Count contracts for given time interval
	 *
	 * @param array $contracts
	 * @param string $date_interval_start
	 * @return int
	 * @throws \Exception
	 */
	private function count_contracts(array $contracts, $date_interval_start)
	{
		$ret = 0;

		foreach ($contracts as $contract) {
			if (($contract->contract_start < $date_interval_start) &&
				($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d') || is_null($contract->contract_end))) {

				$ret++;
			}
		}

		return $ret;
	}

	/**
	 * Returns monthly incomes for each product type
	 *
	 * @param array $contracts
	 * @return array
	 * @throws \Exception
	 */
	private function get_income_total(array $contracts)
	{
		$total = 0.0;
		$ret = array();

		foreach ($contracts as $contract) {
			$items = $contract->items;

			foreach ($items as $item) {
				$product = $item->product;

				if ($product && $product->price != 0) {
					$prepared_data[$product->type][$product->billing_cycle][$product->name][$contract->id]['price'] = $product->price;

					if ($product->type == 'TV') {
						$costcenter = $item->get_costcenter();
						if ($costcenter == null)
							continue;
						$prepared_data[$product->type][$product->billing_cycle][$product->name][$contract->id]['billing_month'] = $costcenter->get_billing_month();
					}
				}
			}
		}

		// calculate income based on type
		foreach ($prepared_data as $product_type => $incomes) {
			foreach ($incomes as $income_cycle => $products) {
				if ($income_cycle == 'Monthly' or $income_cycle == 'Yearly') {
					$ret[$product_type][$income_cycle] = $this->calculate_income($income_cycle, $products);
				}
			}
		}

		// calculate incomes total
		foreach ($ret as $product_type) {
			if (isset($product_type['Monthly'])) {
				$total += $product_type['Monthly'];
			} elseif (isset($product_type['Yearly'])) {
				$total += $product_type['Yearly'];
			}
		}
		$ret['total'] = $total;

		return $ret;
	}

	/**
	 * Calculate income
	 *
	 * @param string $income_cycle
	 * @param array $products
	 * @return float
	 * @throws \Exception
	 */
	private function calculate_income($income_cycle, array $products)
	{
		$monthly = 0.0;
		$yearly = 0.0;
		$once = 0.0;

		switch ($income_cycle)
		{
			case 'Monthly':
				foreach ($products as $product_name => $contracts) {
					foreach ($contracts as $contract_id => $data) {
						$monthly += $data['price'];
					}
				}
				$ret = $monthly;
				break;

			case 'Yearly':
				foreach ($products as $product_name => $contracts) {
					foreach ($contracts as $contract_id => $data) {
						if ($data['billing_month'] == date('m')) {
							$yearly += $data['price'];
						}
					}
				}
				$ret = $yearly;
				break;

			case 'Once':
				foreach ($products as $product_name => $contracts) {
					foreach ($contracts as $contract_id => $data) {
						$once += $data['price'];
					}
				}
				$ret = $once;
				break;
		}

		return $ret;
	}

	/**
	 * Returns rehashed data for the bar chart
	 *
	 * @param array $income
	 * @return array
	 * @throws \Exception
	 */
	private function get_chart_data_income(array $income)
	{
		$ret = array();
		$products = array('Internet', 'Voip', 'TV', 'Other');

		foreach ($products as $product) {

			if (array_key_exists($product, $income)) {
				if (isset($income[$product]['Monthly'])) {
					$data = $income[$product]['Monthly'];
				} elseif (isset($income[$product]['Yearly'])) {
					$data = $income[$product]['Yearly'];
				}
				$val = number_format($data, 2, '.', '');
			} else {
				$val = number_format(0, 2, '.', '');
			}

			if ($product == 'Other') {
				$product = \App\Http\Controllers\BaseViewController::translate_view($product, 'Dashboard');
			}

			$ret['data'][] = $val;
			$ret['labels'][] = $product;
		}

		return $ret;
	}
}