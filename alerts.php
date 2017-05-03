<?php
include 'classes/main.php';

$html = new html('Alerting');
$nav = new navBar(basename(__FILE__));
$html->appendBody($nav->toHTML());

$dbcon = new dbcon();
//$dbcon->query("");
$gridBuilder = new gridRowBuilder(2);

$mailPanel = new panel("Mail", " ");
$syslogPanel = new panel("Syslog", " ");
$gridBuilder->addCol($mailPanel->toHTML());
$gridBuilder->addCol($syslogPanel->toHTML());
$html->appendBody($gridBuilder->toHTML());

echo $html->toHTML();
?>