<?php

// start the session
session_start();

// connect to database
require_once('dbconnection.php');

// include default variables
include 'defaults.php';

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
echo "<table>\n
<tr>\n";

echo "<th><a href='?offset=" . ($offset - $GRIDVIEW_LIMIT) . "'>
<img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?offset=" . ($offset + $GRIDVIEW_LIMIT) . "'>
<img src='images/forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n
</table>\n";

// get candidates
$result = get_sql_result($conn, $sb, $beam_start, $beam_end, $sort, $GRIDVIEW_LIMIT, $offset);

if ( $result->num_rows == 0 ) {
    echo "<p>No candidates match selection.</p>";

} else {
    echo "<div class='flex-container'>\n";

    while ($cand = $result->fetch_assoc()) {
        echo "<div class='container'>\n";

        echo "<div class='text'>ID: " . $cand['cand_id'] .
        ", S/N: " . sprintf("%.1f", $cand['snr']) .
        ", DM: " . sprintf("%.1f", $cand['dm']) .
        ", Beam: " . $cand['beam_number'] .
        "</div>\n";

        if ($cand['dynamic_spectrum']) {
            $dynamic_spectrum = "candidates/" . $cand['dynamic_spectrum'];

            echo "<a href='detailview.php?id=" . $cand['cand_id'] . "'>
            <img class='grid' alt='" . $cand['cand_id'] . "' src='" .
            $dynamic_spectrum .
            "'></a>\n";
        } else {
            echo "<div class='placeholder'>No candidate plot found.</div>\n";
        }

        echo "</div>\n";

    }

    echo "</div>\n";
}

// footer
include 'footer.php';

?>