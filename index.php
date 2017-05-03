<?php
include 'classes/dbcon.php';
include 'classes/bootstrap.php';

$html = new html('Device Status', 'container');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

$html->appendBody('<div class="jumbotron"><h1>Network Manager</h1><p> </p></div>');

$gridBuilder = new gridRowBuilder(2);

// READ PROBE LOG
$file = file("/var/www/html/cronjobs/ping.log");
$logOut = "";
for ($i = max(0, count($file)-10); $i < count($file); $i++) {
  $logOut .= $file[$i] . "<br>";
}
$probeLogPanel = new panel("Probe Log", $logOut);



$gridBuilder->addCol($probeLogPanel->toHTML());

$html->appendBody($gridBuilder->toHTML());
echo $html->toHTML();
