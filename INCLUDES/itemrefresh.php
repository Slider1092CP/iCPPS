<?php

$down = true;

function stribet($inputstr, $delimiterLeft, $delimiterRight) {
    $posLeft = stripos($inputstr, $delimiterLeft) + strlen($delimiterLeft);
    $posRight = stripos($inputstr, $delimiterRight, $posLeft);
    return substr($inputstr, $posLeft, $posRight - $posLeft);
}

if($down == true){

	$localSrc = "http://media1.clubpenguin.com/play/v2/content/local/en/crumbs/local_crumbs.swf";
	$globalSrc = "http://media1.clubpenguin.com/play/v2/content/global/crumbs/global_crumbs.swf";
	$localDest = "local_crumbs.swf";
	$globalDest = "global_crumbs.swf";

	copy($localSrc, $localDest);
	copy($globalSrc, $globalDest);

	exec("/Clubpenguin/Server2/INCLUDES/flare/flare ". $localDest);
	exec("/Clubpenguin/Server2/INCLUDES/flare/flare ". $globalDest);

}
$localCrumbs = file_get_contents("local_crumbs.flr");
$globalCrumbs = file_get_contents("global_crumbs.flr");

$localArray = stribet($localCrumbs, "var paper_crumbs = new Object();", "var igloo_crumbs = new Object();");
$globalArray = stribet($globalCrumbs, "var PAPERDOLLDEPTH_BOTTOM_LAYER = 500;", "var player_colours = new Object();");


$localPlace = array("paper_crumbs[", "] = {'name': '", "'};", "\'");
$localVal = array("", ' => array("name" => "', '"),', "'");
eval('$local = array('.  (str_replace($localPlace, $localVal, $localArray)) . ');');

$global = explode(",", $globalArray);
$global = str_replace("paper_crumbs[", "", $global);
$global = str_replace("]", "", $global);
$global = str_replace("'", "", $global);
$global = str_replace("{", "", $global);
$global = str_replace("    ", "id: ", $global);
$global = str_replace(" = ", " ", $global);

$global = explode("\n", $global);
foreach($global as $foo){
	list($id, $type, $cost, $member) = split(": ", $foo);
}

//global and local should be merged into one array
foreach($local as $i => $value){

	$s = "http://media1.clubpenguin.com/play/v2/content/global/clothing/sprites/$i.swf";
	if (@fclose(@fopen($s, "r"))) {
		//exists
	}
}

?>
