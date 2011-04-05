<?php

$dbHostName = 'localhost';
$dbUserName = 'user_name';
$dbUserPasswd = 'password';
$dbName = 'database_name';

$map_url = 'http://plantconservation.us/cgi-bin/mapserv?&map=/var%2Fwww%2Fvhosts%2Fplantconservation.us%2Fhttpdocs%2Fmaps%2Fplantidapi.map';

$exportDataFilesPath = realpath($app_root . '/../UNIDATA-to-MySQL-EXPORT');

#$debug_show_search = true;

$import_file_encoding = 'cp1250';

// Un-comment the $camera_image_width line to use a fixed column width for the camera image in
// search results.  Otherwise, we'll attempt to get the width of the column from the camera image
// itself, and if that fails, we'll fall back to a pre-defined width.
//$camera_image_width = 60;

?>
