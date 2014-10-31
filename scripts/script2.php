<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
require '../vendor/autoload.php';
require 'simple_html_dom.php';
include 'connect.php';
date_default_timezone_set('America/New_York');

$today = new DateTime("today");
$today = $today->format("Y-m-d");
//echo $today."<br>";

$urlStartDate = new DateTime("today");
$urlStartDate->add(new DateInterval('P1D'));
$urlShortStartDate = $urlStartDate->format("md");
$urlFullStartDate = $urlStartDate->format("Y-m-d");
//echo $urlShortStartDate." ".$urlFullStartDate."<br>";

$urlEndDate = new DateTime("today");
$urlEndDate->add(new DateInterval('P4D'));
$urlShortEndDate = $urlEndDate->format("md");
$urlFullEndDate = $urlEndDate->format("Y-m-d");
//echo $urlShortEndDate." ".$urlFullEndDate."<br>";

$url="http://na.leagueoflegends.com/en/news/store/sales/champion-and-skin-sale-0715-0718";
//http://na.leagueoflegends.com/en/news/store/sales/champion-and-skin-sale-0715-0718

$client = new GuzzleHttp\Client();
$res = $client->get($url);

// Create DOM from URL or file
$html = str_get_html($res->getBody());

//$skins = array();
$champions = array();

// Find all images 
foreach($html->find('img') as $element) :
   	preg_match('@^(?:http://)(\w+)-(\w+)-(\w+).(\w+).(\w+).(\w+)/(\w+)/(\w+)/(\w+)/([a-zA-Z]+)_([a-zA-Z]+)_([a-zA-Z]+)_([a-zA-Z]+).(\w+)@i', $element->src, $matches);
	if ($matches) {
		$skins[$matches[10]] = $matches;
	}

	preg_match('@^(?:http://)(\w+).(\w+).(\w+)/(\w+)/(\w+)/(\w+)/(\w+)/([a-zA-Z]+)@i', $element->src, $matches2);
	if ($matches2) {
		$champions[$matches2[8]] = $matches2;
	}
endforeach;


function getSkins($mysqli, $html, $today, $urlFullEndDate, $urlFullStartDate) {

	//find prices by parsing html body
	foreach($html->find('div[class=field-type-text-with-summary]') as $element) :
		$skin1Name = $element->children(5)->plaintext;
		$skin2Name = $element->children(8)->plaintext;
		$skin3Name = $element->children(11)->plaintext;
		preg_match_all('/(\d{3})/', $element->plaintext, $matches3);
	endforeach;

	//delete last element which is a duplicate
	array_pop($matches3);

	//set arrays
	$skin1 = array('name'=>$skin1Name, 'full_price'=>$matches3[0][0], 'sale_price'=>$matches3[0][1]);
	$skin2 = array('name'=>$skin2Name, 'full_price'=>$matches3[0][2], 'sale_price'=>$matches3[0][3]);
	$skin3 = array('name'=>$skin3Name, 'full_price'=>$matches3[0][4], 'sale_price'=>$matches3[0][5]);

	//combine arrays
	$skinsOnSale = array($skin1, $skin2, $skin3);

	echo "<pre>";
	print_r($skinsOnSale);
	echo "</pre>";

	$int0 = 0;
	$int1 = 1;

	$stmt=$mysqli->prepare("UPDATE skins SET passed=?, last_sale=sale_end_date WHERE passed=?");
	$stmt->bind_param("ii", $int0, $int1);
	$stmt->execute();
	$stmt->close();

	$stmt=$mysqli->prepare("UPDATE skins SET active=?, passed=? WHERE active=?");
	$stmt->bind_param("iii", $int0, $int1, $int1);
	$stmt->execute();
	$stmt->close();

	foreach ($skinsOnSale as $key => $value) {
		echo "<pre>";
		//print_r($value["name"]);
		echo "</pre>";

		$stmt=$mysqli->prepare("UPDATE skins SET insert_date=?, sale_end_date=?, sale_start_date=?, active=?, rp=?, date_sale_string=CONCAT(date_sale_string, ', ".$urlFullEndDate."') WHERE skin=?");
		$stmt->bind_param("sssiis", $today, $urlFullEndDate, $urlFullStartDate, $int1, $value["full_price"], $value["name"]);
		$stmt->execute();
		$stmt->close();

		$stmt=$mysqli->prepare("SELECT id, champion_id FROM skins WHERE skin=?");
		$stmt->bind_param("s", $value['name']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($skin_id, $champion_id);
		while ($stmt->fetch()) {

			echo $skin_id." ".$champion_id."<br>";
			$stmt2=$mysqli->prepare("INSERT INTO skin_sales (skin, original_price, sale_price, start_date, end_date, skin_id, champion_id) VALUES (?,?,?,?,?,?,?)");
			$stmt2->bind_param("siissii", $value['name'], $value['full_price'], $value['sale_price'], $urlFullStartDate, $urlFullEndDate, $skin_id, $champion_id);
			$stmt2->execute();
			$stmt2->close();

		}
		$stmt->close();
	}

	return array($matches3, $skinsOnSale);
}

/**
 * 
 */
function getChampions($mysqli, $champions, $html, $today, $urlFullEndDate, $urlFullStartDate) {

	//find prices by parsing html body
	foreach($html->find('div[class=field-type-text-with-summary]') as $element) :
		$skin1Name = $element->children(5)->plaintext;
		$skin2Name = $element->children(8)->plaintext;
		$skin3Name = $element->children(11)->plaintext;
		preg_match_all('/(\d{3})/', $element->plaintext, $matches3);
	endforeach;

	//delete last element which is a duplicate
	array_pop($matches3);

	//match html champion name (without spaces, special chars ex DrMundo) to db champion name
	$queryChampions = array();
	foreach ($champions as $key => $value) {
		$stmt=$mysqli->prepare("SELECT DISTINCT champion, champion_id FROM skins WHERE REPLACE(`champion`, '. ', '') = ? OR REPLACE(`champion`, ' ', '') = ? OR REPLACE(`champion`, ' \'', '') = ?");
		$stmt->bind_param("sss", $value[8], $value[8], $value[8]);
		$stmt->execute();
		$stmt->bind_result($champion, $champion_id);
		while ($stmt->fetch()) {
			$queryChampions[] = $champion;
		}
		$stmt->close();
	}

	//add to array
	$champion1 = array('name'=>$queryChampions[0], 'full_price'=>$matches3[0][6], 'sale_price'=>$matches3[0][7]);
	$champion2 = array('name'=>$queryChampions[1], 'full_price'=>$matches3[0][8], 'sale_price'=>$matches3[0][9]);
	$champion3 = array('name'=>$queryChampions[2], 'full_price'=>$matches3[0][10], 'sale_price'=>$matches3[0][11]);

	//combine
	$championsOnSale = array($champion1, $champion2, $champion3);

	echo "<pre>";
	print_r($championsOnSale);
	echo "<pre>";

	$int0 = 0;
	$int1 = 1;

	$stmt=$mysqli->prepare("UPDATE champions SET passed=?, last_sale=sale_end_date WHERE passed=?");
	$stmt->bind_param("ii", $int0, $int1);
	$stmt->execute();
	$stmt->close();

	$stmt=$mysqli->prepare("UPDATE champions SET active=?, passed=? WHERE active=?");
	$stmt->bind_param("iii", $int0, $int1, $int1);
	$stmt->execute();
	$stmt->close();

	foreach ($championsOnSale as $key => $value) {

		$stmt=$mysqli->prepare("UPDATE champions SET insert_date=?, sale_end_date=?, sale_start_date=?, active=?, rp=?, date_sale_string=CONCAT(date_sale_string, ', ".$urlFullEndDate."') WHERE champion=?");
		$stmt->bind_param("sssiis", $today, $urlFullEndDate, $urlFullStartDate, $int1, $value["full_price"], $value["name"]);
		$stmt->execute();
		$stmt->close();

		$stmt=$mysqli->prepare("SELECT id FROM champions WHERE champion=?");
		$stmt->bind_param("s", $value['name']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($champion_id);
		while ($stmt->fetch()) {

			echo $champion_id."<br>";
			$stmt2=$mysqli->prepare("INSERT INTO champ_sales (champion, original_price, sale_price, start_date, end_date, champion_id) VALUES (?,?,?,?,?,?)");
			$stmt2->bind_param("siissi", $value['name'], $value['full_price'], $value['sale_price'], $urlFullStartDate, $urlFullEndDate, $champion_id);
			$stmt2->execute();
			$stmt2->close();

		}
		$stmt->close();

	}


	return $championsOnSale;
}

getSkins($mysqli, $html, $today, $urlFullEndDate, $urlFullStartDate);
getChampions($mysqli, $champions, $html, $today, $urlFullEndDate, $urlFullStartDate);







/*try {

    $mandrill = new Mandrill('');
    $message = array(
        'text' => 'Example text content',
        'subject' => 'ScryingOrb Script Results',
        'from_email' => 'test_email@example.com',
        'from_name' => 'Example Name',
        'to' => array(
            array(
                'email' => 'dkarkar@gmail.com',
                'name' => 'Darshan Karkar',
                'type' => 'to'
            )
        )
    );
    $result = $mandrill->messages->send($message);
    print_r($result);

} catch(Mandrill_Error $e) {
    // Mandrill errors are thrown as exceptions
    echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
    throw $e;
}
*/


?>