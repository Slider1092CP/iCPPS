<?php
class Client extends ClientBase{
	public $extRoomID = -1;
	public $intRoomID = -1;
	public $ID;
	public $inGame = false;
	public $xpos = 0;
	public $ypos = 0;
	public $frame = 1;
	public $name = "";
	public $identified = false;
	public $isMuted = false;
	public $time = NULL;
	public $lastItemTime = 0;
	public $recentitems = 0;
	public $crumbsDone = 9001;
	public $crumbsCache = array();
	protected $defaults = array( //Any crumbs you store MUST HAVE A DEFAULT VALUE IN THIS ARRAY!
		'email' => "",
		'registerIP' => "",
		'registertime' => 0,
		'color' => 1,
		'head'	=> 444,//0
		'face'	=> 0,
		'neck'	=> 0,
		'body'	=> 0,
		'hands'	=> 0,
		'feet'	=> 0,
		'pin'	=> 413,
		'photo'	=> 0,
		'items'	=> array(1, 444),
		'coins'	=> 10000,
		'isModerator'	=>	false,
		'isBanned_'	=> false,
		'buddies' => array(),
		'ignore' => array(),
		'stamps' => array(),
		'stampColor' => 1,
		'stampHighlight' => 1,
		'stampPattern' => -1,
		'stampIcon' => 1,
		'igloo' => 1,
		'music' => 0,
		'floor' => 0,
		'furniture' => array(),
		'roomFurniture' => "",
	);
	
	
	function c($p, $set = NULL){
		if($set !== NULL){
			$this->setPlayerCrumb($p, $set);
			return $this->p[$p] = $set;
		}
		$this->p[$p] = $this->getPlayerCrumb($p);
		return $this->p[$p];
	}

	function getPlayerCrumb($p, $id = null){
		if($id === null){
			$id = $this->ID;
		}
		if($p == "id"){
			return $id;
		}
		$a = $this->getPlayerCrumbs();
		if(key_exists($p, $a))
			return $a[$p];
		else
			return NULL;
	}


        function can($what){
            if($this->hasSuperAdmin() == true){
                return true;
            }
            else{
                $p = $this->getPermissions();
                //if($p[$this->makeInt($what)]){
              //
            //}
            }
        }

	function updateIgloo($igloo){
		if(!is_numeric($igloo)){
			return;
		}
		$this->c("roomFurniture", "");
		$this->c("floor", "0");
		$this->c("igloo", $igloo);
		$this->delCoins(1000);
		if($this->extRoomID == $this->ID + 1000){
			$this->write(makeXt("au", $this->intRoomID, $igloo, $this->c("coins")));
		}
	}

	function updateMusic($music){
		if(!is_numeric($music)){
			return;
		}
		$this->c("music", $music);
	}

	function updateFloor($floor){
		if(!is_numeric($floor)){
			return;
		}
		$this->c("floor", $floor);
		$this->delCoins(500);
		if($this->extRoomID == $this->ID + 1000){
			$this->write(makeXt("ag", $this->intRoomID, $floor, $this->c("coins")));
		}
	}

	function getBuddyList(){
		$b = $this->c("buddies");
		$s = "";
		foreach($b as $buddy){
			if(validID($buddy)){
				$s .= "$buddy|" . getName($buddy) . "|";
				if($this->parent->isOnline($buddy)){
					$this->parent->clientsByID[$buddy]->write(makeXt("bon%-1", $this->ID));
					$s .= "1%";
				}
				else{
					$s .= "0%";
				}
			}
		}
		return $s;
	}

	function getIgnoreList(){
		$n = $this->c("ignore");
		$s = "";
		foreach($n as $ignore){
			if(validID($ignore))
			$s .= "$ignore|" . getName($ignore) . "%";
		}
		return $s;
	}

	function getItems($array = false){
		$a = $this->c("items");
		if($array){
			return $a;
		}
		if(!in_array($c = $this->c("color"), $a)){
			$a[] = $c;
			$this->c("items", $a);
		}
		$s = "";
		if(!is_array($a)){
			$a = array($this->c("color"));
		}
		foreach($a as $i){
			$s .= "$i%";
		}
		$s .= "%%";
		$s = str_replace("%%%", "", $s);
		return $s;
	}
	
	function isWearing($id){
		$clothes = array($this->c("head"), $this->c("face"), $this->c("neck"), $this->c("body"), $this->c("hands"), $this->c("feet"));
		foreach($clothes as $value){
			if($value == $id){
				return true;
			}
		}
	}

	function addStamp($stamp){
		$a = $this->c("stamps");
		if(!in_array($stamp, $a)){
			$a[] = $stamp;
			$a = array_values($a);
			$this->c("stamps", $a);
			$this->write(makeXt("sse", "-1", $stamp, $this->c("coins")));
			$this->parent->log->log("{$this->name} added stamp $stamp!");
		}
	}

	function setStampBookCoverDetails($color, $highlight, $pattern, $icon){
		$args = func_get_args();
		foreach($args as $arg){
			if(!is_numeric($arg)){
				return;
			}
		}
		$this->c("stampColor", $color);
		$this->c("stampHighlight", $highlight);
		$this->c("stampPattern", $pattern);
		$this->c("stampIcon", $icon);
	}

	function addFurniture($item, $coins = 100){
		$time = time();
		if($this->lastItemTime > $time){
			if(!$this->c("isModerator")){
				if($this->recentitems >= 3){
					$this->sendError("610%You have been auto<b>kicked</b> for adding furniture too quickly..<b>If you repeat this offence, you will be banned.</b>/\t\n<em>This is not a ban.</em>");
					$this->parent->removeClient($this->clientID);
					return;
				}
				else
					$this->recentitems++;
			}
		}
		else{
			$this->recentitems = 0;
			$this->lastItemTime = $time + 1;
		}
		if(!is_numeric($item)){
			$this->sendError(410);
			return;
		}
		/*if(in_array($item, $this->parent->patchedFurniture) || in_array($item, $this->parent->patchedfurnitureadd)){
			if(!$this->c("isModerator")){
				$this->sendError("610%You have been auto<b>kicked</b> for hacking bait items.<b>If you repeat this offence, you will be banned.</b>/\t\n<em>This is not a ban.</em>");
				$this->parent->removeClient($this->clientid);
				return;
			}
		}*/
		$a = $this->c("furniture");
		if(!is_array($a))
			$a = array();
		if(!key_exists($item, $a)){
			$a[$item] = 1;
			//$a = array_values($a);
			$this->c("furniture", $a);
			if($coins !== NULL){
				//implement coin deduction
				$coins = -$coins;
				$coins += $this->c("coins");
				$coins = $this->c("coins", $coins);
			}
			else{
				$coins = $this->c("coins");
			}
			$this->write(makeXt("af", $this->intRoomID, $item, $coins));
			$this->parent->log->log("{$this->name} added item $item!");
		}
		else{
			$a[$item]++;
			//$a = array_values($a);
			$this->c("furniture", $a);
			if($coins !== NULL){
				//implement coin deduction
				$coins = -$coins;
				$coins += $this->c("coins");
				$coins = $this->c("coins", $coins);
			}
			else{
				$coins = $this->c("coins");
			}
			$this->write(makeXt("af", $this->intRoomID, $item, $coins));
			$this->parent->log->log("{$this->name} added item $item!");
		}
	}

	function getFurniture(){
		$furn = $this->c("furniture");
		$s = "";
		foreach($furn as $key => $val){
			$s .= "%$key|$val";
		}
		$s = substr($s, 1);
		return $s;
	}

	function saveRoomFurniture($str){
		$this->c("roomFurniture", $str);
	}

	function addItem($item, $coins = 100){
		if(!is_numeric($item)){
			$this->sendError(410);
			return;
		}
		$item = trim($item);
		$time = time();
		if($this->lastItemTime > $time){
			if(!$this->c("isModerator")){
				if($this->recentitems >= 3){
					$this->sendError("610%You have been auto<b>kicked</b> for adding items too quickly..<b>If you repeat this offence, you will be banned.</b>/\t\n<em>This is not a ban.</em>");
					$this->parent->removeClient($this->clientID);
					return;
				}
				else
					$this->recentitems++;
			}
		}
		else{
			$this->recentitems = 0;
			$this->lastItemTime = $time + 1;
		}
		if(!is_numeric($item)){
			return;
		}
		if(in_array($item, $this->parent->patched) || in_array($item, $this->parent->patchedadd)){
			if(!$this->c("isModerator")){
				$this->sendError("610%You have been auto<b>kicked</b> for hacking bait items.<b>If you repeat this offence, you will be banned.</b>/\t\n<em>This is not a ban.</em>");
				$this->parent->removeClient($this->clientID);
				return;
			}
		}
		$a = $this->c("items");
		if(!is_array($a))
			$a = array($this->c("color"));
		if(!in_array($item, $a)){
			$a[] = $item;
			$a = array_values($a);
			$this->c("items", $a);
			if($coins !== NULL){
				//implement coin deduction
				$current = $this->c("coins");
				if($current >= $coins){
					$current = $current - $coins;
					$current = $this->c("coins", $current);
				}
				else{
					$this->sendError();
				}
			}
			else{
				$coins = $this->c("coins");
			}
			$this->write(makeXt("ai", $this->intRoomID, $item, $coins));
			$this->parent->log->log("{$this->name} added item $item!");
		}
	}

	function addCoins($coins){
		$coins += $this->c("coins");
		$this->c("coins", $coins);
	}

	function delCoins($coins){
		$coins = -$coins;
		$coins += $this->c("coins");
		$this->c("coins", $coins);
	}

	function onIdentify($ID, $name){
		$this->ID = $ID;
		$this->name = $name;
		$this->loginName = $name;
		$this->parent->clientsByID[$ID] =& $this;
		$this->identified = true;
		$this->requests = array();
		$a = $this->getPlayerCrumbs();
		$diff = false;
		foreach($this->defaults as $k => $d){
			if(!key_exists($k, $a) || $a[$k] === NULL){
				$diff = true;
				$a[$k] = $d;
			}
		}
		if(!$a["isModerator"]){
			foreach($a['items'] as $key => $i){
				if(in_array($i, $this->parent->patched)){
					$diff = true;
					unset($a['items'][$key]);
				}
			}
			if($diff){
				$a['items'] = array_values($a['items']);
			}
			foreach($this->parent->trArt as $arttype){
				if(in_array($a[$arttype], $this->parent->patched)){
					$a[$arttype] = 0;
					$diff = true;
				}
			}
		}
		foreach($a as $k => $v){
			if(!key_exists($k, $this->defaults)){
				$diff = true;
				unset($a[$k]);
			}
		}
		foreach($a['buddies'] as $key => $tb){
			if(!validID($tb)){
				$diff = true;
				unset($a['buddies'][$key]);
			}
		}
		if(!$a['registertime']){
			$a['registertime'] = time();
			$diff = true;
		}
		$this->isModerator = $a['isModerator'];
		if($diff){
			$this->setPlayerCrumbs($a);
		}
	}

	function buildPlayerString(){
		$s = $this->ID;
		$c = $this->getPlayerCrumbs();
        $s .= "|" . $this->name . "|" . 1;//ENGLISH BITMASK!
		$s .= "|" . $c["color"];
		$s .= "|" . $c["head"];
		$s .= "|" . $c["face"];
		$s .= "|" . $c["neck"];
		$s .= "|" . $c["body"];
		$s .= "|" . $c["hands"];
		$s .= "|" . $c["feet"];
		$s .= "|" . $c["pin"];
		$s .= "|" . $c["photo"];
		$s .= "|" . $this->xpos;
		$s .= "|" . $this->ypos;
		$s .= "|" . $this->frame;
		$s .= "|" . 1;
		$s .= "|" . ($c['isModerator'] ? 2159 : 16);
		//$this->parent->log->log("Player string for client {$this->clientID}:{$this->name} created: $s");
		return $s;
	}

	function getPlayerCrumbs(){
		if($this->crumbsDone <= 300){
			++$this->crumbsDone;
			return $this->crumbsCache;
		}
		$a = (getData("SELECT crumbs FROM accs WHERE ID=" . dbEscape($this->ID), "single"));
		if(!$a)
			return BAD_USER;
		$a = unserialize($a['crumbs']);
		if(!is_array($a))
			return BAD_USER;
		$this->crumbsCache = $a;
		$this->crumbsDone = 0;
		return $a;
	}
	
	function setPlayerCrumb($p, $s = NULL){
		$a = $this->getPlayerCrumbs();
		$a[$p] = $s;
		return $this->setPlayerCrumbs($a);
	}

	function setPlayerCrumbs($a){
		if(is_array($a)){
			$this->crumbsCache = $a;
			setData("UPDATE accs SET crumbs = '" . dbEscape(serialize($a)) . "' where ID = '" . dbEscape($this->ID) . "'");
		}
	}

	public function sendError($e = 410){
		$this->write("%xt%e%" . $this->intRoomID . "%$e%");
	}

	public function sendXt(){
		$a = func_get_args();
		if(!is_array($a))
			return false;
		$send = "%xt%";
		foreach($a as $s){
			$send .= $s . "%";
		}
		return $this->write($send);
	}
	
	function __destruct(){
		self::$num--;
		@socket_close($this->sock);
		unset($this->parent);
	}
}

?>
