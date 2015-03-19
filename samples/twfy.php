<?php

// Include the API binding
require_once '../twfyapi.php';

// Set up a new instance of the API binding
$twfyapi = new TWFYAPI('DpPSWnGj7XPRGePtfMGWvGqQ');

// Get a list of Labour MPs in XML format
$mps = $twfyapi->query('getMPs', array('output' => 'xml', 'party' => 'labour'));

// Print out the list
header('Content-type: application/xml');
echo $mps;

?>