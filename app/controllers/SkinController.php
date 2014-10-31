<?php

class SkinController extends BaseController {

	public function skinHistory() {
		$today = new DateTime("today");

		$distinctRP = DB::select("SELECT DISTINCT rp FROM skins ORDER BY rp DESC");
		$skins = DB::select("SELECT * FROM skins");
		$countSkin = count($skins);

		$rp1820 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(1820));
		$rp1350 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(1350));
		$rp975 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(975));
		$rp750 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(750));
		$rp520 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(520));
		$rp390 = DB::select("SELECT * FROM skins WHERE rp=? ORDER BY last_sale ASC", array(390));

		$count1820 = count($rp1820);
		$count1350 = count($rp1350);
		$count975 = count($rp975);
		$count750 = count($rp750);
		$count520 = count($rp520);
		$count390 = count($rp390);

		foreach($distinctRP as $rp):

			foreach(${"rp".$rp->rp} as $skin):

				if ($skin->passed == 1) {
					//$date_last_sale = new DateTime($skin->last_sale);
					$sale_end_date = new DateTime($skin->sale_end_date);
					$interval = $today->diff($sale_end_date);
					$skin->days_past = $interval->format('%a');
					//echo $skin->skin."<br>";
				} else {
					$date_last_sale = new DateTime($skin->last_sale);
					$interval = $today->diff($date_last_sale);
					$skin->days_past = $interval->format('%a');	
				}

				//prediction formula
				if ( ($rp->rp) == "1350") {

					$formula = ${"count".$rp->rp}*(365/(26* 1));

				} elseif ($rp->rp=="975") {

					$formula = ${"count".$rp->rp}*(365/(26* 5));

				} elseif ($rp->rp=="750") {

					$formula = ${"count".$rp->rp}*(365/(26* 2));

				} elseif ($rp->rp=="520") {

					$formula = ${"count".$rp->rp}*(365/(26* 4));

				} else {

					$formula = ${"count".$rp->rp}*(365/$countSkin);

				}

				$days = round($formula);
				$last_sale = $date_last_sale;
				$skin->expected_sale_date_raw = $last_sale->add(new DateInterval('P'.$days.'D'));
				$skin->expected_sale_date = $skin->expected_sale_date_raw->format("M d \'y");

			endforeach;

		endforeach;
		
		return View::make('skin.skinHistory')
			->with('skins', $skins)
			->with('rp1820',$rp1820)
			->with('rp1350',$rp1350)
			->with('rp975',$rp975)
			->with('rp750',$rp750)
			->with('rp520',$rp520)
			->with('rp390',$rp390)
			->with('rp_range', $distinctRP)
			->with('countSkin',$countSkin)
			->with('today', $today);
	}
}

		//$distinctRP = Skin::distinct()->orderBy('rp', 'desc')->get(array('rp'));
		//$countSkin = Skin::all();
		//$countSkin = count($countSkin);

		/*$rp1820 = Skin::where('rp', '=', '1820')->orderBy("last_sale", "asc")->get();
		$rp1350 = Skin::where('rp', '=', '1350')->orderBy("last_sale", "asc")->get();
		$rp975 = Skin::where('rp', '=', '975')->orderBy("last_sale", "asc")->get();
		$rp750 = Skin::where('rp', '=', '750')->orderBy("last_sale", "asc")->get();
		$rp520 = Skin::where('rp', '=', '520')->orderBy("last_sale", "asc")->get();
		$rp390 = Skin::where('rp', '=', '390')->orderBy("last_sale", "asc")->get();*/

		//echo $rp1820;
		//echo $distinctRP;

		//$skins = Skin::all();