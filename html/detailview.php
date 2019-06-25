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
$sql_query = "
SELECT spscandidate.id as cand_id,
spscandidate.viewed,
spscandidate.utc,
spscandidate.mjd,
spscandidate.snr,
spscandidate.dm,
spscandidate.dm_ex,
spscandidate.width,
spscandidate.dynamic_spectrum,

observation.id as obs_id,
observation.field_name as field_name,
observation.utc_start as obs_utc_start,
beamconfig.nbeam,
observation.boresight_ra,
observation.boresight_dec,

beam.number as beam_number, beam.coherent as beam_coherent,
beam.ra, beam.dec, beam.gl, beam.gb,

scheduleblock.sb_id as sb_id,
scheduleblock.sb_id_code as sb_id_code
FROM spscandidate

LEFT JOIN observation_spscandidate
ON spscandidate.id=observation_spscandidate.spscandidate
LEFT JOIN observation
ON observation_spscandidate.observation=observation.id

LEFT JOIN beam_spscandidate
ON spscandidate.id=beam_spscandidate.spscandidate
LEFT JOIN beam
ON beam_spscandidate.beam=beam.id

LEFT JOIN observation_scheduleblock
ON observation.id=observation_scheduleblock.observation
LEFT JOIN scheduleblock
ON observation_scheduleblock.scheduleblock=scheduleblock.id

LEFT JOIN beamconfig_observation
ON observation.id=beamconfig_observation.observation
LEFT JOIN beamconfig
ON beamconfig_observation.beamconfig=beamconfig.id

WHERE spscandidate.id = ?";

$stmt = $conn->prepare($sql_query);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ( $result->num_rows == 0 ) {
    echo "<p>Candidate not found.</p>";

} else {
    $cand = $result->fetch_assoc();

    if ($cand['dynamic_spectrum']) {
        $dynamic_spectrum = "candidates/" . $cand['dynamic_spectrum'];

        echo "<div>\n";
        echo "<img class='detail' alt='dynamic spectrum' src='" . 
        $dynamic_spectrum . "'>\n";
        echo "</div>\n";
    }

    // candidate
    echo "<h3>Candidate</h3>\n";
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>ID: " . $cand['cand_id'] . "</td>\n";
    echo "<td>Views: " . $cand['viewed'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>S/N: " . sprintf("%.2f", $cand['snr']) . "</td>\n";
    echo "<td>Width: " . sprintf("%.2f", $cand['width']) . " ms</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>DM: " . sprintf("%.2f", $cand['dm']) . " pc cm<sup>-3</sup></td>\n";
    //echo "<td>DM/DM<sub>gal</sub>: " . sprintf("%.2f", $cand['dm_ex']) . "</td>\n";
    echo "</tr>\n";
    
    echo "<tr>\n";
    echo "<td colspan=2>UTC: " . $cand['utc'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td colspan=2>MJD: " . sprintf("%.8f", $cand['mjd']) . "</td>\n";
    echo "</tr>\n";
    echo "</tr>\n";
    echo "</table>\n";

    // beam
    echo "<h3>Beam</h3>\n";
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>Number: " . $cand['beam_number'] . "</td>\n";
    echo "<td>Coherent: " . $cand['beam_coherent'] . "</td>\n";
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

    // observation
    echo "<h3>Observation</h3>\n";
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>ID: " . $cand['obs_id'] . "</td>\n";
    echo "<td>UTC start: " . $cand['obs_utc_start'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Total beams: " . $cand['nbeam'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    // boresight
    echo "<h3>Boresight</h3>\n";
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>Field: " . $cand['field_name'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>RA: " . $cand['boresight_ra'] . "</td>\n";
    echo "<td>Dec: " . $cand['boresight_dec'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    // schedule block
    echo "<h3>Schedule Block</h3>\n";
    echo "<table>\n";
    echo "<tr>\n";
    echo "<td>SB: " . $cand['sb_id'] . "</td>\n";
    echo "<td>Code: " . $cand['sb_id_code'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    // register candidate view
    $stmt = $conn->prepare("UPDATE spscandidate SET viewed = ? WHERE id = ?");

    $viewed = $cand['viewed'] + 1;
    $stmt->bind_param("ii", $viewed , $id);

    $stmt->execute();
}

// footer
include 'footer.php';

?>