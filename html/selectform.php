<?php

// figure out pointing and beams
if ( isset($_GET['pointing']) ) {
  $raw_pointing = $_GET['pointing'];
} elseif ( isset($_SESSION['pointing']) ) {
  $raw_pointing = $_SESSION['pointing'];
} else {
  $raw_pointing = 1;
}

if ( !filter_var($raw_pointing, FILTER_VALIDATE_INT) === false ) {
    $pointing = filter_var($raw_pointing, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Pointing is invalid.");
}

// save pointing in session cookie
$_SESSION['pointing'] = $pointing;

// select form
echo "<form>\n";
echo "<label for='pointing'>Pointing:</label>\n";
echo "<input type='text' name='pointing' id='pointing' size='6' maxlength='6' value='" . $pointing . "' />\n";

echo "<label for='beam_start'>Beams:</label>\n";
echo "<input type='text' name='beam_start' id='beam_start' size='4' maxlength='4' />\n";
echo "to";
echo "<input type='text' name='beam_end' id='beam_end' size='4' maxlength='4' />\n";
echo "<input type='submit' value='Submit' />\n";
echo "</form>\n";

?>