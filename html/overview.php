<?php

// connect to database
require_once('dbconnection.php');

// start the session
session_start();

// header
include 'header.php';

// selection form
include 'selectform.php';

// sort form
include 'sortform.php';

// get candidates
$result = $conn->query("SELECT * FROM candidates ORDER BY " . $sort . " LIMIT 100");

while ($cand = $result->fetch_assoc()) {
    echo "<p>Candidate: " . $cand['id'] . ", " . $cand['dm'] . ", " . $cand['dm_ex'] . ", " .
    $cand['snr'] . ", " . $cand['width'] . ", " . $cand['ra'] . ", " . $cand['dec'] . "</p>\n";
}

// footer
include 'footer.php';

?>