<?php
// load json from file
$json = file_get_contents('/raid/webhost/www/meerkatcands.com/config/config.json');
$config = json_decode($json, true);

// connect to database
$conn = new mysqli($config['server'], $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die("Failed to connect to database: " . $conn->connect_error);
}

//$conn->set_charset("utf8");
?>