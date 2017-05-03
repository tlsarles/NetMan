<?php
$myfile = fopen("showwifi.txt", "r") or die("Unable to open file!");
$line = "";
while(!feof($myfile)) {
	$line .= fgets($myfile);
}
$stores = explode("show run access-list FromWireless",$line);
$maxLines = 0;
foreach($stores as $key => $value) {
	$lines = explode("\n",$value);
	echo "<hr>";
	foreach($lines as $innerKey => $innerValue) {
		$pos = strpos($innerValue, "access-list");
		if($pos !== false) {
			$output = substr($innerValue, $pos);
			echo $innerKey." | ".$output."<br>";
			$maxLines = $innerKey;
		}
	}
	$maxArray[] = $maxLines;
}
print_r(array_count_values($maxArray));
?>