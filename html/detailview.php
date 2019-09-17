<?php

// start the session
session_start();

// connect to database
require_once('dbconnection.php');

// include default variables
include 'defaults.php';

// header
include 'header.php';

// figure out id
if ( isset($_GET['id']) ) {
    $raw_id = $_GET['id'];
} elseif ( isset($_SESSION['id']) ) {
    $raw_id = $_SESSION['id'];
} else {
    $raw_id = $START_ID;
}
  
if ( filter_var($raw_id, FILTER_VALIDATE_INT) ) {
    $id = filter_var($raw_id, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("ID is invalid.");
}

// treat negative case
if ( $id < 0 ) {
    $id = $START_ID;
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

echo "<th><a href='?id=" . $START_ID . "'><img src='images/skip-backward-icon.png' border='0'></a></th>\n";
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
observation.field_name,
observation.boresight_ra,
observation.boresight_dec,
observation.utc_start as obs_utc_start,
observation.nant,
observation.receiver,
observation.cfreq,
observation.bw,
observation.nchan,
observation.tsamp,

beamconfig.nbeam,

beam.number as beam_number,
beam.coherent as beam_coherent,
beam.source as beam_source,
beam.ra, beam.dec,
beam.gl, beam.gb,

scheduleblock.sb_id,
scheduleblock.sb_id_mk,
scheduleblock.sb_id_code_mk,
scheduleblock.proposal_id_mk,
scheduleblock.proj,
scheduleblock.utc_start as sb_utc_start,
scheduleblock.sub_array,
scheduleblock.observer,
scheduleblock.description as sb_description,

pipelineconfig.name as pipeline_name,
pipelineconfig.version as pipeline_version,
pipelineconfig.dm_threshold,
pipelineconfig.snr_threshold,
pipelineconfig.width_threshold,
pipelineconfig.zerodm_zapping
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

LEFT JOIN pipelineconfig_spscandidate
ON spscandidate.id=pipelineconfig_spscandidate.spscandidate
LEFT JOIN pipelineconfig
ON pipelineconfig_spscandidate.pipelineconfig=pipelineconfig.id

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

    echo "<div class='flex-container-detail'>\n";

    // candidate
    echo "<div class='container-detail'>\n";
    echo "<h3>Candidate</h3>\n";
    echo "<table>\n";

    echo "<tr>\n";
    echo "<td>ID: " . $cand['cand_id'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>S/N: " . sprintf("%.2f", $cand['snr']) . "</td>\n";
    echo "</tr>\n";
    
    echo "<tr>\n";
    echo "<td>Width: " . sprintf("%.2f", $cand['width']) . " ms</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>DM: " . sprintf("%.2f", $cand['dm']) . " pc cm<sup>-3</sup></td>\n";
    //echo "<td>DM/DM<sub>gal</sub>: " . sprintf("%.2f", $cand['dm_ex']) . "</td>\n";
    echo "</tr>\n";
    
    echo "<tr>\n";
    echo "<td>UTC: " . $cand['utc'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>MJD: " . sprintf("%.10f", $cand['mjd']) . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Views: " . $cand['viewed'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";

    // beam
    echo "<div class='container-detail'>\n";
    echo "<h3>Beam</h3>\n";
    echo "<table>\n";

    echo "<tr>\n";
    echo "<td>Number: " . $cand['beam_number'] . "</td>\n";
    echo "</tr>\n";

    $coherent = $cand['beam_coherent'] ? "True" : "False";
    echo "<tr>\n";
    echo "<td>Coherent: " . $coherent . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Source: " . $cand['beam_source'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>RA: " . $cand['ra'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Dec: " . $cand['dec'] . "</td>\n";
    echo "</tr>\n";

    if ($cand['gl']) {
        echo "<tr>\n";
        echo "<td>Gl: " . sprintf("%.3f", $cand['gl']) . " deg</td>\n";
        echo "</tr>\n";

        echo "<tr>\n";
        echo "<td>Gb: " . sprintf("%.3f", $cand['gb']) . " deg</td>\n";
        echo "</tr>\n";
    }

    echo "</table>\n";
    echo "</div>\n";

    // observation
    echo "<div class='container-detail'>\n";
    echo "<h3>Observation</h3>\n";
    echo "<table>\n";

    echo "<tr>\n";
    echo "<td>ID: " . $cand['obs_id'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>UTC start: " . $cand['obs_utc_start'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Antennas: " . $cand['nant'] . "</td>\n";
    echo "</tr>\n";

    if ($cand['receiver'] == 1) {
        $receiver = "L-band";
    } elseif ($cand['receiver'] == 2) {
        $receiver = "UHF-band";
    } elseif ($cand['receiver'] == 3) {
        $receiver = "S-band";
    } else {
        $receiver = $cand['receiver'];
    }
    echo "<tr>\n";
    echo "<td>Receiver: " . $receiver . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Centre frequency: " . $cand['cfreq'] . " MHz</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Bandwidth: " . $cand['bw'] . " MHz</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Channels: " . $cand['nchan'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Tsamp: " . sprintf("%.2f", ($cand['tsamp'] * 1E6 )) . " us</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Total beams: " . $cand['nbeam'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";

    // boresight
    echo "<div class='container-detail'>\n";
    echo "<h3>Boresight</h3>\n";
    echo "<table>\n";

    echo "<tr>\n";
    echo "<td>Field: " . $cand['field_name'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>RA: " . $cand['boresight_ra'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Dec: " . $cand['boresight_dec'] . "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";

    // schedule block
    echo "<div class='container-detail'>\n";
    echo "<h3>Schedule Block</h3>\n";
    echo "<table>\n";

    echo "<tr>\n";
    echo "<td>ID: " . $cand['sb_id'] . "</td>\n";
    echo "<tr>\n";

    echo "<tr>\n";
    echo "<td>ID MK: " . $cand['sb_id_mk'] . "</td>\n";
    echo "<tr>\n";

    echo "<tr>\n";
    echo "<td>Code MK: " . $cand['sb_id_code_mk'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Proposal: " . $cand['proposal_id_mk'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>UTC start: " . $cand['sb_utc_start'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Project: " . $cand['proj'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Description: " . $cand['sb_description'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Sub-array: " . $cand['sub_array'] . "</td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<td>Contact MK: " . $cand['observer'] . "</td>\n";
    echo "</tr>\n";

    echo "</table>\n";
    echo "</div>\n";

       // pipeline config
       echo "<div class='container-detail'>\n";
       echo "<h3>Pipeline Configuration</h3>\n";
       echo "<table>\n";

       echo "<tr>\n";
       echo "<td>Name: " . $cand['pipeline_name'] . "</td>\n";
       echo "<tr>\n";

       echo "<tr>\n";
       echo "<td>Version: " . $cand['pipeline_version'] . "</td>\n";
       echo "<tr>\n";
   
       echo "<tr>\n";
       echo "<td>DM threshold: " . sprintf("%.1f", $cand['dm_threshold']) . " pc cm<sup>-3</sup></td>\n";
       echo "<tr>\n";

       echo "<tr>\n";
       echo "<td>S/N threshold: " . sprintf("%.1f", $cand['snr_threshold']) . "</td>\n";
       echo "<tr>\n";

       echo "<tr>\n";
       echo "<td>Width threshold: " . sprintf("%.1f", $cand['width_threshold']) . " ms</td>\n";
       echo "<tr>\n";

       $zerodm_zapping = $cand['zerodm_zapping'] ? "True" : "False";
       echo "<tr>\n";
       echo "<td>Zero-DM: " . $zerodm_zapping . "</td>\n";
       echo "<tr>\n";
   
       echo "</table>\n";
       echo "</div>\n";

    echo "</div>";

    // register candidate view
    $stmt = $conn->prepare("UPDATE spscandidate SET viewed = ? WHERE id = ?");

    $viewed = $cand['viewed'] + 1;
    $stmt->bind_param("ii", $viewed , $id);

    $stmt->execute();
}

// footer
include 'footer.php';

?>