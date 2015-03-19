<?php

// Include the API binding
require_once '../oaapi.php';

// Set up a new instance of the API binding
$oaapi = new OAAPI('ChDS2CGxMoPbGzY2taAuEktx');

// Get a list of Labor representatives in XML format
$reps = $oaapi->query('getRepresentatives', array('output' => 'xml', 'party' => 'labor'));

// Print out the list
header('Content-type: application/xml');
echo $reps;

?>