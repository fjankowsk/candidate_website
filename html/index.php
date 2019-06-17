<?php

// connect to database
require_once('dbconnection.php');

// start the session
session_start();

// header
include 'header.php';

// selection form
$select_form = <<<FORM
<form action='index.php'>
  <label for='pointing'>Pointing</label>
  <input type='text' name='pointing' id='pointing' />
  <label for='psr'>Beam</label>
  <input type='text' name='beam' id='beam' />
  <input type='submit' value='Submit' />
</form>

FORM;
echo $select_form;

// sort form
$sort_form = <<<FORM
<form action='index.php'>
  <p>Sort by:
    <select name='sort'>
      <option value='snr'>S/N</option>
      <option value='dm'>DM</option>
      <option value='width'>Width</option>
    </select>
    <input type='submit' value='Submit' />
  </p>
</form>

FORM;
echo $sort_form;

// figure out sorting
$raw_sort = isset($_GET['sort']) ? $_GET['sort'] : null;

switch ($raw_sort):
    case 'snr':
        $sort = 'snr';
        break;
    case 'dm':
        $sort = 'dm';
        break;
    case 'width':
        $sort = 'width';
        break;
    default:
        $sort = "id";
endswitch;

// figure out id
$raw_id = isset($_GET['id']) ? $_GET['id'] : 1;
$id = intval($raw_id);

// navigation
echo "<table>";
echo "<tr>\n";
echo "<th>\n";
echo "<a href='index.php?id=" . 1 . "'><img src='images/skip-backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?id=" . ($id-50) . "'><img src='images/fast-backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?id=" . ($id-1) . "'><img src='images/backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?id=" . ($id+1) . "'><img src='images/forward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?id=" . ($id+50) . "'><img src='images/fast-forward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php'><img src='images/skip-forward-icon.png' border='0'></a>\n";
echo "</th>\n";
echo "</tr>\n";
echo "</table>\n";

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