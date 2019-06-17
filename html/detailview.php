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
  
if ( !filter_var($raw_id, FILTER_VALIDATE_INT) === false ) {
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
echo "<table>\n
<tr>\n";

echo "<th><a href='?id=" . 1 . "'><img src='images/skip-backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id-50) . "'><img src='images/fast-backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id-1) . "'><img src='images/backward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id+1) . "'><img src='images/forward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?id=" . ($id+50) . "'><img src='images/fast-forward-icon.png' border='0'></a></th>\n";
echo "<th><a href='?'><img src='images/skip-forward-icon.png' border='0'></a></th>\n";

echo "</tr>\n
</table>\n";

// get candidates
$result = $conn->query("SELECT * FROM candidates WHERE id = " . $id);

if ( $result->num_rows == 0 ) {
    echo "<p>Candidate not found.</p>";

} else {
    $cand = $result->fetch_assoc();

    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>ID: " . $cand['id'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td colspan=2>UTC: " . $cand['utc'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td colspan=2>MJD: " . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Pointing: " . $cand['pointing'] . "</td>\n";
    echo "<td>Beam: " . $cand['beam'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>S/N: " . $cand['snr'] . "</td>\n";
    echo "<td>Width: " . $cand['width'] . " ms</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>DM: " . $cand['dm'] . " pc cm<sup>-3</sup></td>\n";
    echo "<td>DM/DM<sub>gal</sub>: " . $cand['dm_ex'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>RA: " . $cand['ra'] . "</td>\n";
    echo "<td>Dec: " . $cand['dec'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Gl: " . $cand['gl'] . " deg</td>\n";
    echo "<td>Gb: " . $cand['gb'] . " deg</td>\n";
    echo "</tr>\n";
    
    echo "<tr>\n";
    echo "<td>Viewed: " . $cand['viewed'] . "</td>\n";
    echo "</tr>";
    echo "</table>\n";

    // tf-plot
    if ($cand['tf_plot']) {
        echo '<img width="600" src="data:image;base64,' . base64_encode($cand['tf_plot']) . '">';
    }

    // register candidate view
    // 1) prepare statement
    $stmt = $conn->prepare("UPDATE candidates SET viewed = ? WHERE id = ?");

    $viewed = $cand['viewed'] + 1;
    // 2) bind parameters
    $stmt->bind_param("ii", $viewed , $id);

    // 3) execute
    $stmt->execute();
}

// footer
include 'footer.php';

?>