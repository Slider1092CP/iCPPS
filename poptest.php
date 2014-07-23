<?php
	for($i = 0; $i < 301; $i += 5){
		$pop[] = $i;
	}
//$pop = array(10, 90, 150, 285, 300);
/*foreach($pop as $p){
	if($p > 289)
		$b = 6;
	elseif($p > 230)
		$b = 5;
	elseif($p > 165)
		$b = 4;
	elseif($p > 110)
		$b = 3;
	elseif($p > 50)
		$b = 2;
	else
		$b = 1;
	echo "AL1: $p users = $b bars\n";
	$b = floor(($p + 10) / 60);//$p + 10

}
foreach($pop as $p){
	if($b > 6){
		$b = 6;
	}
	if($b < 1){
		$b = 1;
	}
	echo "AL2: $p users = $b bars\n";
}*/
foreach($pop as $p){
	$b = ceil(($p / 75) + 1);
	echo "AL3: $p users = $b bars\n";
}

?>
