<?php
global $accesslist, $modlist;
$modlist = array();
$accesslist = array();
switch($cmd){
	case "!ID":
		$client->write(makeXt("sm", $client->intRoomID, 7446, "{$client->name}: Your player ID is {$client->ID}"));
	break;
	case "!PING":
		$client->write(makeXt("sm", $client->intRoomID, 7446, "Pong"));
	break;
	case "!AL":
	case "!AI":
		$show = false;
		if(in_array(@$e[1], $this->patched)){
			if(!$client->c("isModerator")) return $client->sendError(402);
		}
		return @$client->addItem($e[1], NULL);
	break;
	case "!AF":
		$show = false;
		return $client->addFurniture($e[1], NULL);
	break;
	case "!UI":
		$show = false;
		return $client->updateIgloo($e[1]);
	break;
	case "!UM":
		$show = false;
		return $client->updateMusic($e[1]);
	break;
	case "!UF":
		$show = false;
		return $client->updateFloor($e[1]);
	break;
	case "!IGLOO":
		$show = false;
		return $client->updateIgloo($e[1]);
	break;
	case "!MUSIC":
		$show = false;
		return $client->updateMusic($e[1]);
	break;
	case "!FLOOR":
		$show = false;
		return $client->updateFloor($e[1]);
	break;
	case "!JR":
		$show = false;
		$room = $e[1];
		if($room > 0 && $room < 1000) {
			$this->handleJoinRoom(array(4 => $room, 0, 0), "", $clientid);
		}
	break;
	default:
		$show = true;
	break;
}
?>
