<?php header('Refresh: 60');

// connect to database
require_once('dbconnection.php');

// start the session
session_start();

// header
$header = <<<HEADER
<html>
<head>
  <title>MeerTRAP Candidate Viewer</title>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  <meta name='author' content='Fabian Jankowski' />
</head>
<body>
  <div align='center'>
  <h1>MeerTRAP Candidate Viewer</h1>

HEADER;
echo $header;

// selection form
$select_form = <<<FORM
<form action='overview.php'>
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
<form action='overview.php'>
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

// get candidates
$result = $conn->query("SELECT * FROM candidates ORDER BY " . $sort . " LIMIT 100");

while ($cand = $result->fetch_assoc()) {
    echo "<p>Candidate: " . $cand['id'] . ", " . $cand['dm'] . ", " . $cand['dm_ex'] . ", " .
    $cand['snr'] . ", " . $cand['width'] . ", " . $cand['ra'] . ", " . $cand['dec'] . "</p>\n";
}

$footer = <<<FOOTER
  </div>
</body>
</html>
FOOTER;

echo $footer;

$conn->close;

?>