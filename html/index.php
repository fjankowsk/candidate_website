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

// // 1) prepare statement
// $stmt = $conn->prepare("SELECT * FROM candidates ORDER BY ?");

// // 2) bind parameters
// $stmt->bind_param("s", $sort);

// // 3) execute
// $stmt->execute();
// print("Test");

// // 4) fetch results
// $result = $stmt->get_result();

// get candidates
$result = $conn->query("SELECT * FROM candidates ORDER BY " . $sort);

while ($cand = $result->fetch_assoc()) {
    echo "<p>Candidate: " . $cand['id'] . ", " . $cand['dm'] . ", " . $cand['dm_ex'] . ", " .
    $cand['snr'] . ", " . $cand['width'] . ", " . $cand['ra'] . ", " . $cand['dec'] . "</p>\n";
}

// navigation
echo "<table>\n<tr>\n<th>\n";
echo "<a href='index.php?i=" . ($n-$num) . "&n=" . $num . "&s=" . $size . "'><img src='images/skip-backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?i=" . ($i+5*$num) . "&n=" . $num . "&s=" . $size . "'><img src='images/fast-backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?i=" . ($i+$num) . "&n=" . $num . "&s=" . $size . "'><img src='images/backward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?i=" . ($i-$num) . "&n=" . $num . "&s=" . $size . "'><img src='images/forward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php?i=" . ($i-5*$num) . "&n=" . $num . "&s=" . $size . "'><img src='images/fast-forward-icon.png' border='0'></a>\n";
echo "</th><th>\n";
echo "<a href='index.php'><img src='images/skip-forward-icon.png' border='0'></a>\n";
echo "</th>\n</tr></table>\n";

$footer = <<<FOOTER
  </div>
</body>
</html>
FOOTER;

echo $footer;

$conn->close;

?>