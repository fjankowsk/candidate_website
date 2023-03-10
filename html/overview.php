<?php

// connect to database
require_once('dbconnection.php');

// start the session
session_start();

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

// display navigation
echo "<table>\n
<tr>\n";

echo "<th><a href='?offset=" . ($offset - $OVERVIEW_LIMIT) . "'>
<img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?offset=" . ($offset + $OVERVIEW_LIMIT) . "'>
<img src='images/forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n
</table>\n";

// get candidates
$result = get_sql_result($conn, $sb, $beam_start, $beam_end, $sort, $OVERVIEW_LIMIT, $offset);

if ( $result->num_rows == 0 ) {
    echo "<p>No candidates match selection.</p>";

} else {
    //echo "<p>" . $result->num_rows . " candidates match selection.</p>\n";

    echo "<table>\n
    <tr>\n
    <th>ID</th>\n
    <th>UTC</th>\n
    <th>S/N</th>\n
    <th>DM</th>\n
    <th>Width</th>\n
    <th>SB</th>\n
    <th>Beam</th>\n
    <th>RA</th>\n
    <th>Dec</th>\n
    <th>Views</th>\n
    </tr>\n";

    echo "<tr>\n
    <td></td>\n
    <td></td>\n
    <td></td>\n
    <td>(pc cm<sup>-3</sup>)</td>\n
    <td>(ms)</td>\n
    <td></td>\n
    <td></td>\n
    <td>(hh:mm:ss)</td>\n
    <td>(dd:mm:ss)</td>\n
    <td></td>\n
    </tr>\n";

    while ($cand = $result->fetch_assoc()) {
        echo "<tr>\n";
        echo "<td><a href='detailview.php?id=" . $cand['cand_id'] . "'>" .
        $cand['cand_id'] . "</a></td>\n";
        echo "<td>" . $cand['utc'] . "</td>\n";
        echo "<td>" . sprintf("%.1f", $cand['snr']) . "</td>\n";
        echo "<td>" . sprintf("%.1f", $cand['dm']) . "</td>\n";
        echo "<td>" . sprintf("%.1f", $cand['width']) . "</td>\n";
        echo "<td>" . $cand['sb_id'] . "</td>\n";
        echo "<td>" . $cand['beam_number'] . "</td>\n";
        echo "<td>" . $cand['ra'] . "</td>\n";
        echo "<td>" . $cand['dec'] . "</td>\n";
        echo "<td>" . $cand['viewed'] . "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

// footer
include 'footer.php';

?>