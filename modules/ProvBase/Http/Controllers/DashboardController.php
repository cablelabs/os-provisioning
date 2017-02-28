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
        $day_period = null;
        $monthly_sales = null;
        $checked = '';
		$show_sales = false;

        try {
			if (App::environment() !== 'production') {
				$show_sales = true;
			}

            // check if the dayfiltet form submitted
            $request = URL::getRequest();
            if ($request->isMethod('post')) {
                $day_period = $request->input('datefilter');
                $monthly_sales = $request->input('switch-sales');
            }

            // get all valid contracts
            $contracts = $this->get_contracts($day_period);

            // get chart data: contracts
            $chart_data_valid_contracts = $this->get_chart_data();
            $chart_data_invalid_contracts = $this->get_chart_data(true);

            // get contracts with products/tariffs
            $itemised_contracts = $this->get_itemised_contracts();

            // sales
            if (is_null($monthly_sales)) {
                $sales = $this->get_sales($itemised_contracts);
                $chart_data_sales = $this->get_chart_data_sales($sales);
            } else {
                $sales = $this->get_sales_monthly($itemised_contracts);
                $chart_data_sales = $this->get_chart_data_sales($sales);
				$checked = 'checked';
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard-Exception: ' . $e->getMessage());
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return View::make(
            'provbase::dashboard', $this->compact_prep_view(
                compact('title', 'contracts', 'chart_data_valid_contracts', 'chart_data_invalid_contracts', 'show_sales', 'sales', 'chart_data_sales', 'checked')
            )
        );
    }

    /**
     * Returns data for the flot chart
     *
     * @param bool $get_inactive
     * @return array
     * @throws \Exception
     */
    private function get_chart_data($get_inactive = false)
    {
        $month = 1;

        try {
            while ($month <= 12) {
                $year = date('Y');
                if ((date('n') - $month) < 0) {
                    $year = $year - 1;
                }

                if ($month < 10) {
                    $month = '0' . $month;
                }

                $contracts[$year][$month] = $this->count_contracts_by_month($month, $year, $get_inactive);
                $month++;
            }
            rsort($contracts);

            foreach ($contracts as $key => $contracts_per_year) {
                foreach ($contracts_per_year as $month => $contracts) {
                    $ret_val[] = $contracts;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
        return $ret_val;
    }

    /**
     * Returns data for the flot bar chart
     *
     * @param $sales
     * @return array
     * @throws \Exception
     */
    private function get_chart_data_sales($sales)
    {
        $sale = 0;
        $ret_val = array();
        $product_types = $this->get_product_types();

        try {
        	if (isset($sales[date('Y')])) {
        		$sales = $sales = $sales[date('Y')]['by_types'];
			} elseif (isset($sales['current_month'])) {
				$sales = $sales = $sales['current_month']['by_types'];
			}

			if (count($sales) > 0) {
				foreach ($product_types as $type) {
					$sale = 0;
					if (isset($sales[$type])) {
						$sale = str_replace(',', '.', $sales[$type]);
					}
					$ret_val[] = array($type, $sale);
				}
			}
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
        return $ret_val;
    }

    /**
     * Returns quantity of contracts by month
     *
     * @param string $month
     * @param string $year
     * @param bool $get_inactive
     * @return array
     * @throws \Exception
     */
    private function count_contracts_by_month($month, $year, $get_inactive = false)
    {
        $ret_val = array();

        try {
            $days = $this->get_number_of_days($month, $year);
            $contract_begin = $year . '-' . $month . '-01';
            $contract_end = $year . '-' . $month . '-' . $days;

            if ($get_inactive === false) {
                $contracts = DB::table('contract')->whereBetween('contract_start', array($contract_begin, $contract_end))->get();
            } else {
                $contracts = DB::table('contract')->whereBetween('contract_end', array($contract_begin, $contract_end))->get();
            }
            $contract_counter = 0;

            if (count($contracts) > 0) {
                foreach ($contracts as $contract) {
                    if ($get_inactive === false) {
                        if ($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d')) {
                            $contract_counter++;
                        }
                    } else {
                        if ($contract->contract_end != '0000-00-00' && $contract->contract_end < date('Y-m-d')) {
                            $contract_counter++;
                        }
                    }
                }
            }

            $ret_val = array($month . '/' . substr($year, -2), $contract_counter);
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
        return $ret_val;
    }

    /**
     * Returns the calculated number of month days
     *
     * @param string $month
     * @param string $year
     * @return false|string
     */
    private function get_number_of_days($month, $year)
    {
        return date("t", mktime(0, 0, 0, str_replace('0', '', $month), 1, $year));
    }

    /**
     * Get all valid contracts
     *
     * @param $day_period Quantity last days
     * @return mixed
     * @throws \Exception
     */
    private function get_contracts($day_period = null)
    {
        try {
            $contracts = Contract::all();

            // get all contracts till now
            foreach ($contracts as $contract) {
                if ($contract->contract_start <= date('Y-m-d') && ($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d'))) {
                    $valid_contracts['till_now'][] = $contract;
                }
            }

            // get all contracts last month
            $month = date('m');
            $year = date('Y');

            if (($month - 1) == 0) {
                $month = 12;
                $year = date('Y') - 1;
            }
            $reference_date = $year . '-' . $month . '-' . date('d');

            if (!is_null($day_period)) {
                // generate reference date for day period filter
                $date = date_create(date('Y-m-d'));
                date_sub($date, date_interval_create_from_date_string($day_period . ' days'));
                $reference_date = date_format($date, 'Y-m-d');

                // set period to array
                $valid_contracts['days'] = $day_period;
            }

            foreach ($contracts as $contract) {
                if ($contract->contract_start <= $reference_date && ($contract->contract_end == '0000-00-00' || $contract->contract_end > $reference_date)) {
                    $valid_contracts['period'][] = $contract;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $valid_contracts;
    }

    /**
     * Returns all valid contracts for the current year and the year before
     *
     * @return array
     * @throws \Exception
     */
    private function get_itemised_contracts()
    {
        $ret_val = array();
        $product_types = $this->get_product_types();

        try {
            $contracts = Contract::all();

            foreach ($contracts as $contract) {
                /**
                 * get all contracts current year till now
                 */
                if ($contract->contract_start <= date('Y-m-d') && ($contract->contract_end == '0000-00-00' || $contract->contract_end > date('Y-m-d'))) {
                    $valid_contracts['2017'][] = $contract;
                }

                /**
                 * get all contracts last year
                 *
                 * contract_start:  between 01.01.2016 and 31.12.2016
                 * contract_end:    0000-00-00, <=2016-12-31, > now
                 */
                $last_year = date('Y') - 1;
                $reference_date = $last_year . '-12-31';

                if ($contract->contract_start <= $reference_date &&
                    ($contract->contract_end == '0000-00-00' || $contract->contract_end <= $reference_date || $contract->contract_end > date('Y-m-d'))) {
                    $valid_contracts[$last_year][] = $contract;
                }
            }

            if (count($valid_contracts) > 0) {
                $contracts = null;

                foreach ($valid_contracts as $year => $contracts) {
                    foreach ($contracts as $key => $contract) {
                        foreach ($product_types as $key => $product_type) {
                            $tmp[$year][$contract->id][$product_type] = $contract->get_valid_tariff($product_type);
                        }
                    }
                }

                // Disable all null-Values
                foreach ($tmp as $year => $contracts) {
                    foreach ($contracts as $contract_id => $product_types) {
                        foreach ($product_types as $type_name => $type_value) {
                            if (!is_null($type_value)) {
                                $ret_val[$year][$contract_id][] = $type_value;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $ret_val;
    }

    /**
     * Returns the total count of sales per year and each product type
     *
     * @param $contracts
     * @return array
     * @throws \Exception
     */
    private function get_sales($contracts)
    {
        $ret_val = array();
//d($contracts);
        try {
            foreach ($contracts as $year => $contracts) {
                $sales = 0;

                foreach ($contracts as $contract_id => $contract_items) {
                    foreach ($contract_items as $contract_item) {
                        $product = Product::find($contract_item->product_id);
                        $sales = $this->count_sales($product, $sales);
                    }
                }
                $ret_val[$year]['total'] = $sales;
                $ret_val[$year]['by_types'] = $this->get_sales_by_product_type($contracts, $year);
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
//d($ret_val);
        return $ret_val;
    }

    private function get_sales_monthly($contracts)
    {
        $result = array(
        	'current_month' => array('total' => 0),
			'last_month' =>  array('total' => 0),
		);
		$dates = config('dates');
//d($dates['current_month'], $dates['next_month']);
        try {
            $contracts = $contracts[date('Y')];

            foreach ($contracts as $contract_id => $items) {
                foreach ($items as $item) {
                    $price_last_month = $item->calculate_price_and_span($dates['last_month'], true);
					$price_current_month = $item->calculate_price_and_span($dates['next_month'], true);

					$product = Product::find($item->product_id);

                    if (!is_null($price_current_month)) {
                        $result['current_month']['total'] += $price_current_month['charge'];
						if (isset($result['current_month']['by_types'][$product->type])) {
							$result['current_month']['by_types'][$product->type] += $price_current_month['charge'];
						} else {
							$result['current_month']['by_types'][$product->type] = $price_current_month['charge'];
						}
                    }

					if (!is_null($price_last_month)) {
						$result['last_month']['total'] += $price_last_month['charge'];
						if (isset($result['last_month']['by_types'][$product->type])) {
							$result['last_month']['by_types'][$product->type] += $price_current_month['charge'];
						} else {
							$result['last_month']['by_types'][$product->type] = $price_current_month['charge'];
						}
					}
                }
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }
//d($result);
		return $result;
    }

    /**
     * Returns the total count of sales for each/itemised by product type
     *
     * @param $contracts
     * @return array
     * @throws \Exception
     */
    private function get_sales_by_product_type($contracts)
    {
        $ret_val = array();

        try {
            foreach ($contracts as $contract_id => $contract_items) {

                foreach ($contract_items as $contract_item) {

                    $product = Product::find($contract_item->product_id);

                    $sales = 0;
                    if (isset($ret_val[$product->type])) {
                        $sales = $ret_val[$product->type];
                    }
                    $sales = $this->count_sales($product, $sales);
                }
                $ret_val[$product->type] = $sales;
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $ret_val;
    }

    /**
     * Returns the total count of sales for product types
     *
     * @param $product
     * @param $sales
     * @return string
     * @throws \Exception
     */
    private function count_sales($product, $sales)
    {
        try {
            switch ($product->billing_cycle) {
                case 'Once':
                    $sales = $sales + $product->price;
                    break;

                case 'Yearly':
                    $sales = $sales + $product->price;
                    break;

                case 'Monthly':
                    $sales = $sales + ($product->price * 12);
                    break;
            }
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }

        return number_format($sales, 2, ',', '');
    }

    /**
     * Returns an array of available product types
     *
     * @return array
     * @throws \Exception
     */
    private function get_product_types()
    {
        $ret_val = array();

        try {
            $ret_val = Product::get_product_types();
        } catch (\Exception $e) {
            throw new \Exception(__METHOD__ . ': ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $ret_val;
    }
}