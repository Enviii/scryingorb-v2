<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);

date_default_timezone_set('America/New_York');
include 'connect.php';

$ch = curl_init();
$fp = fopen("output.html", "w");

$urlStartDate=date("md");
$urlStartDate=new DateTime("now");
$urlStartDate->add(new DateInterval('P1D'));
//echo $urlStartDate."<br>";
$urlStartDate=$urlStartDate->format("md");

$today=new DateTime("now");
$todaysDate = $today->format('Y-m-d');

$dateTime = new DateTime("now");
$dateTime->add(new DateInterval('P4D'));

$urlEndDate= $dateTime->format("md");

$url="http://na.leagueoflegends.com/en/news/store/sales/champion-and-skin-sale-".$urlStartDate."-".$urlEndDate;
//$url="http://na.leagueoflegends.com/en/news/store/sales/champion-and-skin-sale-0523-0526";
echo $url;

curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FILE, $fp);

//if (curl_error($ch))
    //die(curl_error($ch));

$output = curl_exec($ch);
$returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
fwrite($fp, $output);

curl_close($ch);
fclose($fp);

//echo "<br>code: ".$returnCode." is return code";
echo "<br><br>\n\n";

if ($returnCode!=200) {
	exit("Error with URL. HTTP Code: ".$returnCode);
}

/* Check if inserts were already made current days sale */
$stmt=$mysqli->prepare("SELECT start_date FROM champ_sales ORDER BY start_date desc LIMIT 1");
$stmt->execute();
$stmt->bind_result($start_date);
$stmt->fetch();
$dateTomorrow = new DateTime("now");
$dateTomorrow->add(new DateInterval("P1D"));
$dateTomorrow=$dateTomorrow->format("Y-m-d");
if ($start_date==$dateTomorrow) {
	exit("Already inserted for the day.");
}

$stmt->close();


$doc = new DOMDocument();
@$doc->loadHTMLFile("output.html");
$xpath = new DOMXPath($doc);

//get entire div that contains a ton of messy crap
$entireText = $xpath->query('//div[@class="field field-name-body field-type-text-with-summary field-label-hidden"]');
foreach ($entireText as $text) {
    $cleanText = mb_convert_encoding($text->textContent, 'HTML-ENTITIES', 'UTF-8');
    $cleanText = str_replace("&nbsp;", '', $cleanText);
    //echo $cleanText;

	$explodedText = preg_split('/[\s]+/', $cleanText);
    foreach ($explodedText as $key => $value) {
    	//echo $key." - ".$value."<br>";
    }
}

echo "<br>";

//get h4 with skin champ names
$skinArr = array();
$skinName = $xpath->query('//div[@class="field field-name-body field-type-text-with-summary field-label-hidden"]/h4');
foreach ($skinName as $skin) {
	//var_dump($skin->textContent);
	$skinArr[]=$skin->textContent;

	// split with " " so i can unset from explodedTest
    $explodedSkin = explode(" ", $skin->textContent);
    foreach ($explodedSkin as $key => $value) {
    	//echo $key." - ".$value."<br>";
    	unset($explodedText[array_search($value,$explodedText)]);
    }
}

echo "<br>";

//get striked out prices
$skinXprice = $xpath->query('//div[@class="field field-name-body field-type-text-with-summary field-label-hidden"]/strike');
foreach ($skinXprice as $xprice) {
    //var_dump($xprice->textContent);
    //printf($tag->textContent);
    //echo $tag->textContent;
}
echo "<br>";

//get skin img
$imgArr = array();
$skinImg = $xpath->query('//div[@class="field field-name-body field-type-text-with-summary field-label-hidden"]/div[@class="gs-container default-2-col"]/div/span/a/@href');
foreach ($skinImg as $img) {
    //var_dump($img->textContent);
    $imgArr[]=$img->textContent;
    //printf($tag->textContent);
    //echo $tag->textContent;
}
echo "<br>";

//get skin img
$champArr = array();
$champImg = $xpath->query('//div[@class="field field-name-body field-type-text-with-summary field-label-hidden"]/div[@class="gs-container default-3-col"]/div/span/a/img/@src');
foreach ($champImg as $champ) {
    //var_dump($champ->textContent);
    $champArr[]=$champ->textContent;
    //printf($tag->textContent);
    //echo $tag->textContent;
}
echo "<br>";

//Get rid of the meaningless text. Split and unset it from explodedTest
$grabText = "Grab these champions and skins on sale for 50% off for a limited time: Skin SalesGive your champions a new look with these skins: RPChampion SalesAdd these champions to your roster: RP RP RP RP RP ";
$grabSplit = explode(" ", $grabText);
foreach ($grabSplit as $key => $value) {
	unset($explodedText[array_search($value,$explodedText)]);
}

//echo explodedTest to see final values
$explodedText = array_values($explodedText);
foreach ($explodedText as $key => $value) {
	echo $key." - ".$value."<br>";
}

/////////////////////////////////////////
////////////////////////////////////////
//////////final results here///////////
///////////////////////////////////////
//////////////////////////////////////
echo "<br>";
echo "<br>";

printf("Champion: ".$explodedText[6] ." Original Price: ". $explodedText[7] ." Sale Price: ". $explodedText[8] ." <img height='150' src=".$champArr[0]."> ");
echo "<br>";
printf("Champion: ".$explodedText[9] ." Original Price: ". $explodedText[10] ." Sale Price: ". $explodedText[11]." <img height='150' src=".$champArr[1]."> ");
echo "<br>";
printf("Champion: ".$explodedText[12] ." Original Price: ". $explodedText[13] ." Sale Price: ". $explodedText[14]." <img height='150' src=".$champArr[2]."> ");

echo "<br>";
echo "<br>";
echo "<br>";

printf("Skin: ".$skinArr[0] ." Original Price: ". $explodedText[0] ." Sale Price: ". $explodedText[1] ." <img height='100' src=".$imgArr[0]."> <img height='100' src=".$imgArr[1]."> ");
echo "<br>";
echo "<br>";
echo "<br>";
printf("Skin: ".$skinArr[1] ." Original Price: ". $explodedText[2] ." Sale Price: ". $explodedText[3] ." <img height='100' src=".$imgArr[2]."> <img height='100' src=".$imgArr[3]."> ");
echo "<br>";
echo "<br>";
echo "<br>";
printf("Skin: ".$skinArr[2] ." Original Price: ". $explodedText[4] ." Sale Price: ". $explodedText[5] ." <img height='100' src=".$imgArr[4]."> <img height='100' src=".$imgArr[5]."> ");


$champ1 = $explodedText[6];
$champ2 = $explodedText[9];
$champ3 = $explodedText[12];

$champNameArr=array();
$champNameArr[]=$champ1;
$champNameArr[]=$champ2;
$champNameArr[]=$champ3;


$champ_sale1 = $explodedText[8];
$champ_sale2 = $explodedText[11];
$champ_sale3 = $explodedText[14];

$champ_og1 = $explodedText[7];
$champ_og2 = $explodedText[10];
$champ_og3 = $explodedText[13];

$champ_img1 = $champArr[0];
$champ_img2 = $champArr[1];
$champ_img3 = $champArr[2];

// $string_version = implode(',', $explodedText[6]);
// echo $string_version;

// echo "<br>";
// echo "<br>";
// //echo $champ1;

// $imageVar = 0;
// for ($i=6; $i <=8 ; $i++) { 
// 	//echo "for champ1 ".$champ1."<br>";
// 	echo $explodedText[$i]."<br>";
// 	echo $

// }





// for ($i=1; $i <=3 ; $i++) { 
// 	$stmt=$mysqli->prepare("INSERT INTO champ_sales (champion, original_price, sale_price, image) VALUES (?,?,?,?)");
// 	$stmt->bind_param("siis", $champ.$i, $champ_img.$i, $champ_sale.$i, $champ_og.$i);
// 	$stmt->execute();
// 	$stmt->close();
// }

// $stmt=$mysqli->prepare("SELECT champion FROM  champ_sales");
// $stmt->execute();
// $stmt->bind_result($champion);
// $stmt->fetch();
// 	echo $champion." champ here ";
// $stmt->close;




// $sixTo14=6;
// for ($i=0; $i <= 2; $i++) { 
// 	echo $explodedText[$sixTo14];
// 	echo "<br>";
// 	echo $champArr[$i];
// 	$sixTo14+=1;
// }




//messy foreach

// $index = 0;
// foreach ($skinName as $key => $value) {
// 	echo "Skin ".$value->textContent." Original Price: ".$explodedText[$index];
// 	echo "<img height='150' src=".$imgArr[$index].">";
// 	$index+=1;
// 	echo " Sale Price: ".$explodedText[$index];
// 	echo "<img height='150' src=".$imgArr[$index].">";
// 	echo "<br>";
// 	$index+=1;
// }

// $index2=0;
// foreach ($skinName as $key => $value) {
// 	echo $skinArr[$index2].$explodedText[$index2].$imgArr[$index2];
// 	$index+=1;
// 	echo $skinArr[$index2].$explodedText[$index2].$imgArr[$index2];
// }




/////////////////////////////////////////////////////////
///////////get start and end date from url///////////////

preg_match("/(?P<text>\.*)sale-(?P<date1>\d{4})-(?P<date2>\d{4})/", $url, $results);


$startFirst2 = substr($results[2], 0, 2);
$startTheRest = substr($results[2], 2);
$endFirst2 = substr($results[3], 0, 2);
$endTheRest = substr($results[3], 2);
echo "<br>";

$startDate = date("Y")."-".$startFirst2."-".$startTheRest;
$endDate = date("Y")."-".$endFirst2."-".$endTheRest;

echo "<br>";
echo "Start Date: ".$startDate;
echo "<br>";
echo "End Date: ".$endDate;

print_r($champNameArr);

$champIdNameArr=array();
foreach ($champNameArr as $championName) {
	//echo $championName;
	$stmt=$mysqli->prepare("SELECT id, champion FROM champions WHERE champion=?");
	$stmt->bind_param("s", $championName);
	$stmt->execute();
	$stmt->bind_result($id, $champion);
	while($stmt->fetch()){
		$champIdNameArr['id'][]=$id;
		$champIdNameArr['champion'][]=$champion;
		echo $id."<br>";
		echo $champion."<br><br>";
	}
	$stmt->close();
}

$skinIdNameArr=array();
foreach ($skinArr as $skinName) {
	$stmt=$mysqli->prepare("SELECT id, skin, champion_id FROM skins WHERE skin=?");
	$stmt->bind_param("s", $skinName);
	$stmt->execute();
	$stmt->bind_result($id, $skin, $champion_id);
	while($stmt->fetch()){
		$skinIdNameArr['id'][]=$id;
		$skinIdNameArr['skin'][]=$skin;
		$skinIdNameArr['champion_id'][]=$champion_id;
		echo $skin."<br>";
		echo $skin."<br><br>";
	}
	$stmt->close();
}

?>

<pre><?php echo print_r($skinIdNameArr) ?></pre>


<?php

echo "champidnamearr 0: ".$champIdNameArr['id']['0']."<br>";

/*foreach ($champIdNameArr as $key => $value) {
	echo $key." key<br><br>";
	echo $value['champion']." value<br><br>";
}*/

echo "enddate".$endDate."<br>";

//champ inserts
//champ 1
$stmt=$mysqli->prepare("INSERT INTO champ_sales (champion, original_price, sale_price, image, start_date, end_date, champion_id) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("siisssi", $champIdNameArr['champion']['0'], $explodedText[7], $explodedText[8], $champArr[0], $startDate, $endDate, $champIdNameArr['id']['0']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE champions SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $champIdNameArr['id']['0']);
$stmt->execute();
$stmt->close();

//champ 2
$stmt=$mysqli->prepare("INSERT INTO champ_sales (champion, original_price, sale_price, image, start_date, end_date, champion_id) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("siisssi", $champIdNameArr['champion']['1'], $explodedText[10], $explodedText[11], $champArr[1], $startDate, $endDate, $champIdNameArr['id']['1']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE champions SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $champIdNameArr['id']['1']);
$stmt->execute();
$stmt->close();

//champ 3
$stmt=$mysqli->prepare("INSERT INTO champ_sales (champion, original_price, sale_price, image, start_date, end_date, champion_id) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("siisssi", $champIdNameArr['champion']['2'], $explodedText[13], $explodedText[14], $champArr[2], $startDate, $endDate, $champIdNameArr['id']['2']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE champions SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $champIdNameArr['id']['2']);
$stmt->execute();
$stmt->close();

//echo $skinArr[0]. $explodedText[0]. $explodedText[1]. $imgArr[0]. $imgArr[1].$startDate.$endDate.$skinIdNameArr['id']['0'];
echo $todaysDate;
//skin inserts
//skin1
$stmt=$mysqli->prepare("INSERT INTO skin_sales (skin, original_price, sale_price, image, image_2, start_date, end_date, skin_id, champion_id) VALUES (?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("siissssii", $skinArr[0], $explodedText[0], $explodedText[1], $imgArr[0], $imgArr[1], $startDate, $endDate, $skinIdNameArr['id']['0'], $skinIdNameArr['champion_id']['0']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE skins SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $skinIdNameArr['id']['0']);
$stmt->execute();
$stmt->close();

//skin2
$stmt=$mysqli->prepare("INSERT INTO skin_sales (skin, original_price, sale_price, image, image_2, start_date, end_date, skin_id, champion_id) VALUES (?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("siissssii", $skinArr[1], $explodedText[2], $explodedText[3], $imgArr[2], $imgArr[3], $startDate, $endDate, $skinIdNameArr['id']['1'], $skinIdNameArr['champion_id']['1']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE skins SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $skinIdNameArr['id']['1']);
$stmt->execute();
$stmt->close();

//skin3
$stmt=$mysqli->prepare("INSERT INTO skin_sales (skin, original_price, sale_price, image, image_2, start_date, end_date, skin_id, champion_id) VALUES (?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("siissssii", $skinArr[2], $explodedText[4], $explodedText[5], $imgArr[4], $imgArr[5], $startDate, $endDate, $skinIdNameArr['id']['2'], $skinIdNameArr['champion_id']['2']);
$stmt->execute();
$stmt->close();

$stmt=$mysqli->prepare("UPDATE skins SET insert_date=?, sale_end_date=?, date_sale_string=CONCAT(date_sale_string, ', ".$endDate."') WHERE id=?");
$stmt->bind_param("ssi", $todaysDate, $endDate, $skinIdNameArr['id']['2']);
$stmt->execute();
$stmt->close();


//get inserted rows from 7 days ago, and change last_sale to sale_end_date which was updated 7 days ago. 
$insert_date = new DateTime("now");
$insert_date->sub(new DateInterval('P7D'));
$insert_date=$insert_date->format("Y-m-d");

$stmt=$mysqli->prepare("SELECT id, skin, insert_date FROM skins WHERE insert_date=?");
$stmt->bind_param("s", $insert_date);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $skin, $inserted_date);
while ($stmt->fetch()) {
	echo $id." ".$inserted_date."<br>";

	$stmt2=$mysqli->prepare("UPDATE skins SET last_sale=sale_end_date WHERE id=?");
	$stmt2->bind_param("i", $id);
	$stmt2->execute();
	$stmt2->close();
}

$stmt=$mysqli->prepare("SELECT id, champion, insert_date FROM champions WHERE insert_date=?");
$stmt->bind_param("s", $insert_date);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $champion, $inserted_date);
while ($stmt->fetch()) {
	echo $id." ".$inserted_date."<br>";

	$stmt2=$mysqli->prepare("UPDATE champions SET last_sale=sale_end_date WHERE id=?");
	$stmt2->bind_param("i", $id);
	$stmt2->execute();
	$stmt2->close();
}



echo count($skinArr);




?>