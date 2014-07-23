<?php
$handlers = array("sm", "handleSendMessage",
	"jr",	"handleJoinRoom",
	"ai",	"handleAddItem",
	"jg",	"handleJoinGame",
	"glr",	"handleGetLatestRevision",
	'l',	"handleLogin",
	'e',	"handleError",
	'h',	"handlePong",
	'zm',	"handleSendMove",
	'gz',	"handleGetGame",
	'jz',	"handleJoinGame",
	'lz',	"handleLeaveGame",
	'uz',	"handleUpdateGame",
	'sz',	"handleStartGame",
	'cz',	"handleCloseGame",
	'zo',	"handleGameOver",
	'gw',	"handleGetWaddleList",
	'jw',	"handleJoinWaddle",
	'lw',	"handleLeaveWaddle",
	'uw',	"handleUpdateWaddle",
	'sw',	"handleStartWaddle",
	'gb',	"handleGetBuddyList",
	'gn',	"handleGetIgnoreList",
	'gi',	"handleGetItemList",
	'go',	"handleGetBuddyOnlineList",
	'gp',	"handleGetPlayer",
	'gf',	"handleGetFurnitureList",
	'gt',	"handleGetTable",
	'sa',	"handleSendAction",
	'se',	"handleSendEmote",
	'sj',	"handleSendJoke",
	'sm',	"handleSendMessage",
	'sq',	"handleSendQuickMessage",
	'ss',	"handleSendSafeMessage",
	'sg',	"handleSendTourGuide",
	'sp',	"handleSendPosition",
	'sf',	"handleSendFrame",
	'st',	"handleSendTeleport",
	'sb',	"handleSendThrowBall",
	'sc',	"handleSendCard",
	'sl',	"handleSendLineMessage",
	'mm',	"handleModMessage",
	'br',	"handleBuddyRequest",
	'ba',	"handleBuddyAccept",
	'bd',	"handleBuddyDecline",
	'bm',	"handleBuddyMessage",
	'rb',	"handleBuddyRemove",
	'bf',	"handleBuddyFind",
	'up',	"handleUpdatePlayerArt",
	'ut',	"handleUpdateTable",
	'jg',	"handleGetPlayerRoom",
	'ai',	"handleAddPlayerItem",
	'af',	"handleAddPlayerFurniture",
	'au',	"handleAddPlayerRoomUpgrade",
	'ag',	"handleAddPlayerRoomFloor",
	'ac',	"handleAddCoin",
	'uc',	"handleUpdateCoins",
	'gc',	"handleGetCoins",
	'ap',	"handleAddPlayer",
	'rp',	"handleRemovePlayer",
	'at',	"handleAddToy",
	'rt',	"handleRemoveToy",
	'cw',	"handleCheckWord",
	'js',	"handleJoinServer",
	'jt',	"handleJoinTable",
	'jr',	"handleJoinPlayerRoom",
	'gu',	"handleGetPuffleList",
);
$odd = false;
foreach($handlers as $handle){
	$odd = !$odd;
	if($odd){
		$toset = $handle;
	}
	else{
		$tmp[$toset] = $handle;
	}
}
$handlers = $tmp;
unset($tmp, $odd, $handle);
print_r($handlers);
?>
