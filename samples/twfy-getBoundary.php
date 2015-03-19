<?php

// Include the API binding
require_once '../twfyapi.php';

// Set up a new instance of the API binding
$twfyapi = new TWFYAPI('DpPSWnGj7XPRGePtfMGWvGqQ');

// Constituency name
$constituency = "Macclesfield";

// Get the constituency's boundary map in KML format (via http://mapit.mysociety.org)
$boundary = $twfyapi->query('getBoundary', array('name' => $constituency));

// Serve the boundary map
header('Content-Type: application/vnd.google-earth.kml+xml; encoding=utf-8');
header('Content-Disposition: attachment; filename="TheyWorkForYou ' . $constituency . ' Constituency Boundary Map.kml"');
echo $boundary;

?>