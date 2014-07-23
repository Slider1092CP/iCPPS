<?php
class ClientBase{
	static $num = 0;
	static $count = 0;
	public $properties = array();
	public $p = null;
	public $sock;
	public $parent;
	public $room;
	public $uniqueid = 0;
	public $inGame = false;
	public $xpos = 0;
	public $ypos = 0;
	public $clientID = 0;
	public $name = "";

	function __construct($sock, $server, $clientid){
		self::$num++;
		self::$count++;
		$this->parent =& $server;
		$this->uniqueid = self::$count;
		$this->sock = $sock;
		$this->p =& $this->properties;//p and properties are for temporary properties.
		$this->p['rndK'] = $this->makeRndK();
		$this->clientID = $clientid;
	}
	
	/*function __destruct(){
		self::$num--;
		@socket_close($this->sock);
	}*/
	
	function write($data, $flags = MSG_EOR){
		$data .= chr(0);
		$sendLen = strlen($data);
		$w = array($this->sock);
		$a = null;
		$b = null;
		$res = @socket_select($a, $w, $b, 0.15);
		if($res === false){
			return $this->parent->removeClient($this->clientid);
		}
		$len = @socket_send($this->sock, $data, $sendLen, $flags);
		//$times = 5;
		/*while($len === false && $len < $sendLen){
			if($len === false){
				$ltr = 0;
			}
			else
				$ltr = $len;
			$data = substr($data, $ltr);
			$sendLen = strlen($data);
			$len = @socket_send($this->sock, $data, $sendLen, $flags);
			usleep(2000);
			$times--;
			if($times < 0)
				break;
		}*/
		return $len ? true : false;
	}

	function makeRndK(){
		$c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789?~";
		$l = rand(6,14);
		$s = "";
		for(;$l > 0;$l--)
			$s .= $c{rand(0,strlen($c) - 1)};
		return $s;
	}

	function buildClientString($type = "raw", $s = "%"){
		if($type == "xml"){
			return $this->buildXmlPlayer();
		}
		return $this->buildRawPlayer($s);
	}

	function getSortedProperties(){
		
	}

	function buildRawPlayer($s){
		return implode($this->getSortedProperties(), $s);
	}

	function getSocket(){
		return $this->sock;
	}
}
?>
