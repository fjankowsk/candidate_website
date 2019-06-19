<?php

// figure out sorting
if ( isset($_GET['sort']) ) {
    $raw_sort = $_GET['sort'];
} elseif ( isset($_SESSION['sort']) ) {
    $raw_sort = $_SESSION['sort'];
} else {
    $raw_sort = 'id';
}

if ( filter_var($raw_sort, FILTER_SANITIZE_STRING) ) {
    $raw_sort = filter_var($raw_sort, FILTER_SANITIZE_STRING);
} else {
    die("Sort is invalid.");
}

switch ($raw_sort):
    case 'utc':
        $sort = 'utc';
        break;
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

// save sorting in session cookie
$_SESSION['sort'] = $sort;

// figure out pointing
if ( isset($_GET['pointing']) ) {
  $raw_pointing = $_GET['pointing'];
} elseif ( isset($_SESSION['pointing']) ) {
  $raw_pointing = $_SESSION['pointing'];
} else {
  $raw_pointing = null;
}

$filter_opts = array("options" => array("min_range"=>1));

if ( $raw_pointing == null ) {
    $pointing = null;
} elseif ( filter_var($raw_pointing, FILTER_VALIDATE_INT, $filter_opts) ) {
    $pointing = filter_var($raw_pointing, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Pointing is invalid.");
}

// save pointing in session cookie
$_SESSION['pointing'] = $pointing;

// figure out beam_start
if ( isset($_GET['beam_start']) ) {
  $raw_beam_start = $_GET['beam_start'];
} elseif ( isset($_SESSION['beam_start']) ) {
  $raw_beam_start = $_SESSION['beam_start'];
} else {
  $raw_beam_start = null;
}

$filter_opts = array("options" => array("min_range"=>1, "max_range"=>400));

if ( $raw_beam_start == null ) {
    $beam_start = null;
} elseif ( filter_var($raw_beam_start, FILTER_VALIDATE_INT, $filter_opts) ) {
    $beam_start = filter_var($raw_beam_start, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Beam start is invalid.");
}

// figure out beam_end
if ( isset($_GET['beam_end']) ) {
    $raw_beam_end = $_GET['beam_end'];
} elseif ( isset($_SESSION['beam_end']) ) {
    $raw_beam_end = $_SESSION['beam_end'];
} else {
    $raw_beam_end = null;
}

$filter_opts = array("options" => array("min_range"=>1, "max_range"=>400));

if ( $raw_beam_end == null ) {
    $beam_end = null;
} elseif ( filter_var($raw_beam_end, FILTER_VALIDATE_INT, $filter_opts) ) {
    $beam_end = filter_var($raw_beam_end, FILTER_SANITIZE_NUMBER_INT);
} else {
    die("Beam end is invalid.");
}

// sanity check beam start and end
if ( ($beam_start != null) && ($beam_end != null) ){
    if ( $beam_start < $beam_end ) {
    } else {
        die("Beam end must be greater than beam start.");
    }
}

// save beam start and end in session cookie
$_SESSION['beam_start'] = $beam_start;
$_SESSION['beam_end'] = $beam_end;

// sorting options
$sort_options = array(
    "utc" => "UTC",
    "snr" => "S/N",
    "dm" => "DM",
    "width" => "Width",
);

// form
echo "<form>\n";
echo "<label for='pointing'>Pointing:</label>\n";
echo "<input type='text' name='pointing' id='pointing' size='6' maxlength='6' value='" . $pointing . "' />\n";

echo "<label for='beam_start'>&nbsp;Beams:</label>\n";
echo "<input type='text' name='beam_start' id='beam_start' size='4' maxlength='4' value='" .
$beam_start . "' />\n";
echo "to";
echo "<input type='text' name='beam_end' id='beam_end' size='4' maxlength='4' value='" .
$beam_end . "' />\n";

echo "&nbsp;Sort by:\n";
echo "<select name='sort'>\n";

foreach($sort_options as $key => $value) {
  if ($key == $sort) {
      echo "<option value='" . $key . "' selected>" . $value . "</option>\n";
  } else {
      echo "<option value='" . $key . "'>" . $value . "</option>\n";
  }
}

echo "</select>\n";
//echo "</div>\n";

echo "<input type='submit' value='Submit' />\n";
echo "</form>\n";

?>