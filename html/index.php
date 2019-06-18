<?php

// start the session
session_start();

// connect to database
require_once('dbconnection.php');

// header
include 'header.php';

// selection form
include 'selectform.php';

// sort form
include 'sortform.php';

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
$limit = 6;
echo "<table>\n
<tr>\n";

echo "<th><a href='?offset=" . ($offset - $limit) . "'>
<img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?offset=" . ($offset + $limit) . "'>
<img src='images/forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n
</table>\n";

// get candidates
$sql = get_sql_query($pointing, $beam_start, $beam_end, $sort, $limit, $offset);
//echo "<p>SQL query: " . $sql . "</p>\n";

$result = $conn->query($sql);

if ( $result->num_rows == 0 ) {
    echo "<p>No candidates match selection.</p>";

} else {
    echo "<div class='row'>\n";
    echo "<div class='column'>\n";

    $number = 1;

    while ($cand = $result->fetch_assoc()) {
        // tf-plot
        if ($cand['tf_plot']) {
            echo "<a href='detailview.php?id=" . $cand['id'] . "'>
            <img width='400' src='data:image;base64," . base64_encode($cand['tf_plot']) . "'></a>\n";
        } else {
            echo "<a href='detailview.php?id=" . $cand['id'] . "'><div width='400'>&nbsp;</div></a>\n";
        }

        if ( ($number % 3) == 0 ) {
            echo "</div>\n";
            echo "<div class='column'>\n";
        }

        $number++;
    }

    echo "</div>\n";
    echo "</div>\n";
}

// footer
include 'footer.php';

?>