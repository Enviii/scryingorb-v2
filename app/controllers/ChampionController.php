<?php

class ChampionController extends BaseController {

	public function championHistory() {
		$today = new DateTime("today");

		$distinctRP = DB::select("SELECT DISTINCT ip FROM champions ORDER BY ip DESC");
		$countChamp = DB::select("SELECT * FROM champions WHERE status = ?", array(3));
		$countChamp = count($countChamp);
		$ip6300 = DB::select("SELECT * FROM champions WHERE ip = ? ORDER BY last_sale ASC", array(6300));
		$ip4800 = DB::select("SELECT * FROM champions WHERE ip = ? ORDER BY last_sale ASC", array(4800));
		$ip3150 = DB::select("SELECT * FROM champions WHERE ip = ? ORDER BY last_sale ASC", array(3150));
		$ip1350 = DB::select("SELECT * FROM champions WHERE ip = ? ORDER BY last_sale ASC", array(1350));
		$ip450 = DB::select("SELECT * FROM champions WHERE ip = ? ORDER BY last_sale ASC", array(450));

		$countip6300 = count($ip6300);
		$countip4800 = count($ip4800);
		$countip450 = count($ip450);

		//combine 3150 and 1350
		$countComb = count($ip3150)+count($ip1350);
		$countip3150 = $countComb;
		$countip1350 = $countComb;

		function clean($string) {
		   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
		   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		}

		function cleanandlower($string) {
		   $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
		   $string = strtolower($string);
		   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		}

		foreach($distinctRP as $ip):

			foreach(${"ip".$ip->ip} as $champ):

				if ($champ->passed == 1) {
					//$date_last_sale = new DateTime($champ->last_sale);
					$sale_end_date = new DateTime($champ->sale_end_date);
					$interval = $today->diff($sale_end_date);
					$champ->days_past = $interval->format('%a');
					//echo $champ->champ."<br>";
				} else {
					$date_last_sale = new DateTime($champ->last_sale);
					$interval = $today->diff($date_last_sale);
					$champ->days_past = $interval->format('%a');	
				}

				$rp = ${"countip".$ip->ip};
				//Debugbar::info($countChamp);
				$formula = $rp*(365/$countChamp);
				$days = round($formula);
				$last_sale = $date_last_sale;
				$champ->expected_sale_date_raw = $last_sale->add(new DateInterval('P'.$days.'D'));
				$champ->expected_sale_date = $champ->expected_sale_date_raw->format("M d \'y");

			endforeach;

		endforeach;

		return View::make('champion.championHistory')
			->with('ip6300', $ip6300)
			->with('ip4800', $ip4800)
			->with('ip3150', $ip3150)
			->with('ip1350', $ip1350)
			->with('ip450', $ip450)
			->with('ip_range', $distinctRP)
			->with('count6300', $countip6300)
			->with('count4800', $countip4800)
			->with('count3150', $countip3150)
			->with('count1350', $countip1350)
			->with('countChamp', $countChamp)
			->with('divCount', $divCount=2)
			->with('today', $today);
	}

}