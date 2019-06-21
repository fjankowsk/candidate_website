<?php

// start the session
session_start();

// connect to database
require_once('dbconnection.php');

// header
include 'header.php';

// selection form
include 'selectform.php';

// helper functions
include 'helpers.php';

// get offset
if ( isset($_GET['offset']) ) {
    $raw_offset = $_GET['offset'];
} else {
    $raw_offset = 0;
}
  
if ( filter_var($raw_offset, FILTER_VALIDATE_INT) === 0 || filter_var($raw_offset, FILTER_VALIDATE_INT) ) {
    $offset = filter_var($raw_offset, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Offset is invalid.");
}

// treat negative case
if ( $offset < 0 ) {
    $offset = 0;
}

// navigation
$limit = 4;
echo "<table>\n
<tr>\n";

echo "<th><a href='?offset=" . ($offset - $limit) . "'>
<img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?offset=" . ($offset + $limit) . "'>
<img src='images/forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n
</table>\n";

// get candidates
$result = get_sql_result($conn, $pointing, $beam_start, $beam_end, $sort, $limit, $offset);

if ( $result->num_rows == 0 ) {
    echo "<p>No candidates match selection.</p>";

} else {
    echo "<div class='flex-container'>\n";

    while ($cand = $result->fetch_assoc()) {
        echo "<div class='container'>\n";

        echo "<div class='text'>ID: " . $cand['id'] .
        ", S/N: " . sprintf("%.1f", $cand['snr']) .
        ", DM: " . sprintf("%.1f", $cand['dm']) .
        ", Beam: " . $cand['beam'] .
        "</div>\n";

        echo "<a href='detailview.php?id=" . $cand['id'] . "'>
        <img class='grid' alt='" . $cand['id'] . "' src='" .
        $cand['tf_plot'] .
        "'></a>\n";

        echo "</div>\n";

    }

    echo "</div>\n";
}

// footer
include 'footer.php';

?>