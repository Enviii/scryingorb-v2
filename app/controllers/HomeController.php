<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome() {
		return View::make('hello');
	}

	function weekdayData() {
		$today = new DateTime("now");
		$latestEndDate=new DateTime("now");
		$latestStartDate=new DateTime("now");
		$weekday=$today->format("l");
		//echo $weekday;

		if ($weekday=="Sunday") {
			$latestStartDate->sub(new DateInterval("P2D"));
			$latestEndDate->sub(new DateInterval("P2D"));
			//echo "hello sunday";
		} elseif ($weekday=="Monday") {
			$latestStartDate->sub(new DateInterval("P3D"));
			$latestEndDate->sub(new DateInterval("P3D"));
			//echo "hello monday";
		} elseif ($weekday=="Tuesday") {
			$latestStartDate=new DateTime("now");
			$latestEndDate->sub(new DateInterval("P1D"));
			//echo "hello tuesday";
		} elseif ($weekday=="Wednesday") {
			$latestStartDate->sub(new DateInterval("P1D"));
			$latestEndDate->sub(new DateInterval("P2D"));
			//echo "hello Wednesday";
		} elseif ($weekday=="Thursday") {
			$latestStartDate->sub(new DateInterval("P2D"));
			$latestEndDate->sub(new DateInterval("P3D"));
			//echo "hello Thursday";
		} elseif ($weekday=="Friday") {
			$latestStartDate=new DateTime("now");
			$latestEndDate=new DateTime("now");
			//echo "hello friday";
		} elseif ($weekday=="Saturday") {
			$latestStartDate->sub(new DateInterval("P1D"));
			$latestEndDate->sub(new DateInterval("P1D"));
			//echo "hello Saturday";
		}

		$lastSaleEndDate = $latestEndDate->format("Y-m-d");
		$lastSaleStartDate = $latestStartDate->format("Y-m-d");

		return array('lastSaleEndDate' => $lastSaleEndDate, 'lastSaleStartDate' => $lastSaleStartDate);
	}

	function CurrentSaleData() {
		$dateData = $this->weekdayData();

		$champ_sales = DB::select("SELECT * FROM champ_sales s INNER JOIN champions c ON c.id=s.champion_id WHERE start_date = ? ORDER BY sale_price DESC LIMIT 3", array($dateData['lastSaleStartDate']));
		$skin_sales = DB::select("SELECT * FROM skin_sales s INNER JOIN skins sk ON sk.id=s.skin_id WHERE start_date = ? ORDER BY sale_price DESC LIMIT 3", array($dateData['lastSaleStartDate']));

/*		$old_champ_sale = DB::select("SELECT * FROM champ_sales WHERE end_date = ? ORDER BY sale_price DESC LIMIT 3", array($lastSaleEndDate));
		$old_skin_sale = DB::select("SELECT * FROM skin_sales WHERE end_date = ? ORDER BY sale_price DESC LIMIT 3", array($lastSaleEndDate));*/

		$champ_sale_array = array();
		$skin_sales_array = array();

		//calculate interval and add to array for view
		foreach ($champ_sales as $key => $value) {
			$today = new DateTime("today");
			$last_sale_calc = new DateTime($value->last_sale);
			$interval = $last_sale_calc->diff($today);
			$interval = $interval->format('%R%a days');
			$value->interval = $interval;
			$champ_sale_array[]=$value;
		}

		Debugbar::info($champ_sales);

		//calculate interval and add to array for view
		foreach ($skin_sales as $key => $value) {
			$today = new DateTime("today");
			$last_sale_calc = new DateTime($value->last_sale);
			$interval = $last_sale_calc->diff($today);
			$interval = $interval->format('%R%a days');
			$value->interval = $interval;
			$skin_sales_array[]=$value;
		}

		$current_sale_start_date = new DateTime($champ_sales[0]->start_date);
		$current_sale_end_date = new DateTime($champ_sales[0]->end_date);

		$current_sale_start_date = $current_sale_start_date->format("M j");
		$current_sale_end_date = $current_sale_end_date->format("M j");

		return array(
			'champ_sales' => $champ_sales, 
			'skin_sales' => $skin_sales, 
			'current_sale_end_date' => $current_sale_end_date, 
			'current_sale_start_date' => $current_sale_start_date);

/*		return View::make('home')
			->with('champ_sales', $champ_sales)
			->with('skin_sales', $skin_sales)
			->with('current_sale_end_date', $current_sale_end_date)
			->with('current_sale_start_date', $current_sale_start_date)
		;*/
	}

	public function home() {

		$currentSaleData = $this->currentSaleData();

		$today = new DateTime("now");
		$today = $today->format("l");

		//Debugbar::info($current_sale_end_date);
		$currentSaleData['today']=$today;

		return View::make('home', $currentSaleData);

		// return View::make('home')
		// 	->with('champ_sales', $champ_sales)
		// 	->with('skin_sales', $skin_sales)
		// 	->with('current_sale_end_date', $current_sale_end_date)
		// 	->with('current_sale_start_date', $current_sale_start_date)
		// 	->with('today', $today)
		// ;
	}

	public function getCurrentSale() {

		$currentSaleData = $this->currentSaleData();

/*		$today = new DateTime("now");
		$today = $today->format("l");
		$currentSaleData['today']=$today;*/

		return View::make('saleContent', $currentSaleData);
	}

	public function getOldSale() {

		$dateData = $this->weekdayData();

		$champ_sales = DB::select("SELECT * FROM champ_sales s INNER JOIN champions c ON c.id=s.champion_id WHERE end_date = ? ORDER BY sale_price DESC LIMIT 3", array($dateData['lastSaleEndDate']));
		$skin_sales = DB::select("SELECT * FROM skin_sales s INNER JOIN skins sk ON sk.id=s.skin_id WHERE end_date = ? ORDER BY sale_price DESC LIMIT 3", array($dateData['lastSaleEndDate']));

		$champ_sale_array = array();
		$skin_sales_array = array();

		//calculate interval and add to array for view
		foreach ($champ_sales as $key => $value) {
			$today = new DateTime("today");
			$last_sale_calc = new DateTime($value->last_sale);
			$interval = $last_sale_calc->diff($today);
			$interval = $interval->format('%R%a days');
			$value->interval = $interval;
			$champ_sale_array[]=$value;
		}

		Debugbar::info($champ_sales);

		//calculate interval and add to array for view
		foreach ($skin_sales as $key => $value) {
			$today = new DateTime("today");
			$last_sale_calc = new DateTime($value->last_sale);
			$interval = $last_sale_calc->diff($today);
			$interval = $interval->format('%R%a days');
			$value->interval = $interval;
			$skin_sales_array[]=$value;
		}

		$old_sale_start_date = new DateTime($champ_sales[0]->start_date);
		$old_sale_end_date = new DateTime($champ_sales[0]->end_date);

		$old_sale_start_date = $old_sale_start_date->format("M j");
		$old_sale_end_date = $old_sale_end_date->format("M j");

		$json = array();
		$json['old_sale_start_date'][] = $old_sale_start_date;
		$json['old_sale_end_date'][] = $old_sale_end_date;
		$json['champ_sales'][] = $champ_sales;
		$json['skin_sales'][] = $skin_sales;

		$json = json_encode($json);

		//return $json;
		return View::make('saleContent')
			->with('champ_sales', $champ_sales)
			->with('skin_sales', $skin_sales)
			->with('current_sale_end_date', $old_sale_end_date)
			->with('current_sale_start_date', $old_sale_start_date)
		;
	}

}
