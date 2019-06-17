<?php

// figure out sorting
if ( isset($_GET['sort']) ) {
  $raw_sort = $_GET['sort'];
} elseif ( isset($_SESSION['sort']) ) {
  $raw_sort = $_SESSION['sort'];
} else {
  $raw_sort = 'id';
}

if ( !filter_var($raw_sort, FILTER_SANITIZE_STRING) === false ) {
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

// sort form
$sort_options = array(
  "utc" => "UTC",
  "snr" => "S/N",
  "dm" => "DM",
  "width" => "Width",
);

echo "<form>\n";
echo "<p>Sort by:\n";
echo "<select name='sort'>\n";

foreach($sort_options as $key => $value) {
  if ($key == $sort) {
      echo "<option value='" . $key . "' selected>" . $value . "</option>\n";
  } else {
      echo "<option value='" . $key . "'>" . $value . "</option>\n";
  }
}

echo "</select>\n";
echo "<input type='submit' value='Submit' />\n";
echo "</p>\n";
echo "</form>\n";

?>