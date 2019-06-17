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

// get candidates
if ( $pointing === null ) {
    $result = $conn->query("SELECT * FROM candidates ORDER BY " . $sort . " DESC LIMIT 100 OFFSET " . $offset);
} else {
    $result = $conn->query("SELECT * FROM candidates WHERE pointing = " . $pointing .
    " ORDER BY " . $sort . " DESC LIMIT 100 OFFSET " . $offset);
}

if ( $result->num_rows == 0 ) {
    echo "<p>No candidates match selection.</p>";

} else {
    echo "<p>" . $result->num_rows . " candidates match selection.</p>\n";

    if ( $result->num_rows >= 100 ) {
        // display navigation
        echo "<table>\n
        <tr>\n";

        echo "<th><a href='?offset=" . ($offset - 100) . "'>
        <img src='images/backward-icon.png' border='0'></a></th>\n";
        echo "<th><a href='?offset=" . ($offset + 100) . "'>
        <img src='images/forward-icon.png' border='0'></a></th>\n";

        echo "</tr>\n
        </table>\n";
    }

    echo "<table>\n
    <tr>\n
    <th>ID</th>\n
    <th>UTC</th>\n
    <th>S/N</th>\n
    <th>DM</th>\n
    <th>Width</th>\n
    <th>Pointing</th>\n
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
        echo "<td><a href='detailview.php?id=" . $cand['id'] . "'>" . $cand['id'] . "</a></td>\n";
        echo "<td>" . $cand['utc'] . "</td>\n";
        echo "<td>" . $cand['snr'] . "</td>\n";
        echo "<td>" . $cand['dm'] . "</td>\n";
        echo "<td>" . $cand['width'] . "</td>\n";
        echo "<td>" . $cand['pointing'] . "</td>\n";
        echo "<td>" . $cand['beam'] . "</td>\n";
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