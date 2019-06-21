<?php

// start the session
session_start();

// connect to database
require_once('dbconnection.php');

// header
include 'header.php';

// figure out id
if ( isset($_GET['id']) ) {
    $raw_id = $_GET['id'];
} elseif ( isset($_SESSION['id']) ) {
    $raw_id = $_SESSION['id'];
} else {
    $raw_id = 1;
}
  
if ( filter_var($raw_id, FILTER_VALIDATE_INT) ) {
    $id = filter_var($raw_id, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("ID is invalid.");
}

// treat negative case
if ( $id < 0 ) {
    $id = 1;
}
  
// save id in session cookie
$_SESSION['id'] = $id;

// id form
echo "<form>\n";
echo "<label for='id'>ID:</label>\n";
echo "<input type='text' name='id' id='id' size='10' maxlength='10' value='" . $id . "' />\n";
echo "<input type='submit' value='Submit' />\n";

// navigation
echo "<table>\n";
echo "<tr>\n";

echo "<th><a href='?id=" . 1 . "'><img src='images/skip-backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id-50) . "'><img src='images/fast-backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id-1) . "'><img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id+1) . "'><img src='images/forward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id+50) . "'><img src='images/fast-forward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?'><img src='images/skip-forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n";
echo "</table>\n";

// get candidates
$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ( $result->num_rows == 0 ) {
    echo "<p>Candidate not found.</p>";

} else {
    $cand = $result->fetch_assoc();

    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>ID: " . $cand['id'] . "</td>\n";
    echo "<td>Viewed: " . $cand['viewed'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td colspan=2>UTC: " . $cand['utc'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td colspan=2>MJD: " . sprintf("%.8f", $cand['mjd']) . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Pointing: " . $cand['pointing'] . "</td>\n";
    echo "<td>Beam: " . $cand['beam'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>S/N: " . sprintf("%.2f", $cand['snr']) . "</td>\n";
    echo "<td>Width: " . sprintf("%.2f", $cand['width']) . " ms</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>DM: " . sprintf("%.2f", $cand['dm']) . " pc cm<sup>-3</sup></td>\n";
    echo "<td>DM/DM<sub>gal</sub>: " . sprintf("%.2f", $cand['dm_ex']) . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>RA: " . $cand['ra'] . "</td>\n";
    echo "<td>Dec: " . $cand['dec'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Gl: " . sprintf("%.3f", $cand['gl']) . " deg</td>\n";
    echo "<td>Gb: " . sprintf("%.3f", $cand['gb']) . " deg</td>\n";
    echo "</tr>\n";

    echo "</table>\n";

    if ($cand['tf_plot']) {
        echo "<div>\n";
        echo "<img class='detail' alt='tf-plot' src='" . $cand['tf_plot'] . "'>\n";
        echo "</div>\n";
    }

    // register candidate view
    $stmt = $conn->prepare("UPDATE candidates SET viewed = ? WHERE id = ?");

    $viewed = $cand['viewed'] + 1;
    $stmt->bind_param("ii", $viewed , $id);

    $stmt->execute();
}

// footer
include 'footer.php';

?>