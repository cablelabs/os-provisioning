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

		$contracts = $income = $chart_data_contracts = $chart_data_income = array();

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

		// get chart data: contracts
		$chart_data_contracts = self::get_chart_data_contracts();
		$contracts = end($chart_data_contracts['contracts']);

		// income
		if (\PPModule::is_active('billingbase') && $allowed_to_see['accounting'])
		{
			$chart_data_income = self::get_chart_data_income();

			// TODO: move income total calculation to get_chart_data_income() and return well structured array 
			// to use in blade -> content of this if-clause will only be the one line ahead
			$income['total'] = 0;
			foreach ($chart_data_income['data'] as $value)
				$income['total'] += $value;
			$income['total'] = (int) $income['total'];
		}

		return View::make('provbase::dashboard', $this->compact_prep_view(
				compact('title', 'contracts', 'chart_data_contracts', 'income', 'chart_data_income', 'allowed_to_see')
			)
		);
	}

	/**
	 * Get all today valid contracts
	 *
	 * @return mixed
	 */
	public static function get_valid_contracts()
	{
		$query = Contract::where('contract_start', '<', date('Y-m-d'))
				->where(function ($query) { $query
				->where('contract_end', '>', date('Y-m-d'))
				->orWhere('contract_end', '=', '0000-00-00')
				->orWhereNull('contract_end');})
				->orderBy('contract_start');

		return \PPModule::is_active('billingbase') ? $query->with('items', 'items.product')->get()->all() : $query->get()->all();
	}


	/**
	 * Generate date by given period
	 *
	 * @param string $period
	 * @param integer $days
	 * @return false|string
	 */
	private function generate_reference_date($period = null, $days = null)
	{
		if (is_null($period))
			return date('Y-m-d');

		$month = date('m');
		$year = date('Y');

		switch ($period)
		{
			case 'lastMonth':
				$time = strtotime('last month');
				$ret  = date('Y-m-'.date('t', $time), $time);
				break;

			case 'dayPeriod':
				$ret = date('Y-m-d', strtotime("-$days days"));
				break;
		}

		return $ret;
	}

	/**
	 * Returns rehashed data for the line chart
	 *
	 * @param array $contracts
	 * @return array
	 */
	private static function get_chart_data_contracts()
	{
		$ret = array();
		$i 	 = 13;

		while($i > 0)
		{
			$i--;
			$time = strtotime("-$i month");

			$ret['labels'][] = date('m/Y', $time);
			$ret['contracts'][] = self::count_contracts(date('Y-m-01', $time));
		}

		return $ret;
	}

	/**
	 * Count contracts for given time interval
	 *
	 * @param array $contracts
	 * @param string $date_interval_start
	 * @return int
	 */
	private static function count_contracts($date_interval_start)
	{
		$ret = 0;

		// for 800 contracts this is approximately 4x faster
		$ret = Contract::where('contract_start', '<', $date_interval_start)
				->where(function ($query) { $query
				->where('contract_end', '>', date('Y-m-d'))
				->orWhere('contract_end', '=', '0000-00-00')
				->orWhereNull('contract_end');})
				->count();

		// foreach ($contracts as $contract) {
		// 	if (($contract->contract_start < $date_interval_start) &&
		// 		($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d') || is_null($contract->contract_end))) {
		// 		$ret++;
		// 	}
		// }

		return $ret;
	}


	/**
	 * Returns monthly incomes for each product type
	 *
	 * @param array $contracts
	 * @return array
	 */
	public static function get_income_total()
	{
		$contracts = self::get_valid_contracts();

		// manipulate dates array for charge calculation for coming month (not last one)
		$conf  = \Modules\BillingBase\Entities\BillingBase::first();
		$dates = \Modules\BillingBase\Console\accountingCommand::create_dates_array();

		$dates['lastm_Y'] 	= date('Y-m');
		$dates['lastm_01'] 	= date('Y-m-01');
		$dates['thism_01'] 	= date('Y-m-01', strtotime('next month'));
		$dates['lastm'] 	= date('m');
		$dates['Y'] 		= date('Y');
		$dates['m'] 		= date('m', strtotime('next month'));

		foreach ($contracts as $c)
		{
			if (!$c->costcenter || !$c->create_invoice)
				continue;

			$c->expires = date('Y-m-01', strtotime($c->contract_end)) == $dates['lastm_01'];

			foreach ($c->items as $item)
			{
				if (!isset($item->product))
					continue;

				$cycle 	  = $item->get_billing_cycle();
				$interval = strtotime('first day of this month');

				if (!$item->check_validity($cycle, $interval))
					continue;

				$item->calculate_price_and_span($dates, false, false);

				// $prepared_data[$product->type][$cycle][$product->name][$c->id]['price'] = $item->charge;
				// prepared data not really needed - why cycle ?? - TODO: simplify
				if (!isset($ret[$item->product->type][$cycle])) {
					$ret[$item->product->type][$cycle] = $item->charge;
					continue;
				}

				$ret[$item->product->type][$cycle] += $item->charge;
			}
		}

		$total = 0;
		foreach ($ret as $cycle) {
			foreach ($cycle as $value) {
				$total += $value;
			}
		}

		// Net income total - TODO: calculate gross ?
		$ret['total'] = $total;

		return $ret;

	}


	/**
	 * Calculate Income for current month, format and save to json
	 * Used by Cronjob
	 */
	public static function save_income_to_json()
	{
		$dir_path = storage_path("app/data/dashboard/");
		$fn = 'income.json';

		$income = self::get_income_total();
		$income = self::format_chart_data_income($income);

		if (!is_dir($dir_path))
			mkdir($dir_path, 0740, true);

		\File::put($dir_path.$fn, json_encode($income));

		system("chown apache $dir_path");
	}


	/**
	 * Get chart data from json file - created by cron job
	 *
	 * @return array
	 */
	public static function get_chart_data_income()
	{
		$dir_path = storage_path("app/data/dashboard/");
		$fn = 'income.json';

		return json_decode(\File::get($dir_path.$fn), true);
	}



	/**
	 * Returns rehashed data for the bar chart
	 *
	 * @param array $income
	 * @return array
	 */
	private static function format_chart_data_income(array $income)
	{
		$ret = array();
		$products = array('Internet', 'Voip', 'TV', 'Other');

		// TODO: why differentiate between monthly and yearly ??
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