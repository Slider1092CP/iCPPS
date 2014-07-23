<?php
class parser{
	function __construct(){
		require("rooms.php");
		foreach($this->rooms as $key => $room){
			echo "$key:{$room['name']}\n";
		}
	}
}
$p = new parser;
?>
