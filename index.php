<?php header('Refresh: 60');
  // Connection info
require_once('../roach/inc/connect.php');
// Start the session
session_start();

// Connect to DB
mysql_connect($server,$username,$password);
mysql_query("set character_set_connection=utf8,character_set_database=utf8");
mysql_select_db($database) or die( "Unable to select database");

// Header 
echo "<html>\n<head>\n";
echo "<title>Jodrell Bank Observatory: DFB Data Archive | Jodrell Bank Centre for Astrophysics</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />\n";
echo "<meta name='author' content='Cees Bassa, Ben Stappers, Christine Jordan' />\n";
echo "\n</head>\n<body bgcolor='#BBBBBB'>\n";

// Get observation number
$i = (isset($_GET['i']) ? $_GET['i'] : null);
// Get pulsar
$psr = (isset($_GET['psr']) ? $_GET['psr'] : null);
// Get number of plots to display
$num = (isset($_GET['n']) ? $_GET['n'] : null);
// Get size
$size = (isset($_GET['s']) ? $_GET['s'] : null);

// Error checking
if ($i==NULL)
  $i = 0;
if ($num==NULL)
  $num = 1;
if ($size==NULL)
  $size = 0;

// Read directory contents
$dir = ".";
$odir = opendir($dir);
$n = 0;
$files = array();
while($file = readdir($odir)) {
  if ($size == 0) {
    if (substr($file,-8) == "_sft.png") {
      $fileroot = substr($file,0,-8);
      if ($psr!=NULL) {
	if (strstr($fileroot,$psr)) {
	  $n++;
	  $files[$n]=$fileroot;
	}
      } else {
	$n++;
	$files[$n]=$fileroot;
      }
    }
  } else {
    if (substr($file,-10) == "_sft_s.png") {
      $fileroot = substr($file,0,-10);
      if ($psr!=NULL) {
	if (strstr($fileroot,$psr)) {
	  $n++;
	  $files[$n]=$fileroot;
	}
      } else {
	$n++;
	$files[$n]=$fileroot;
      }
    }
  }
}
sort($files);

$j = $n-$i-1;

echo "<div align='center'>\n";

// Instruments
echo "<p>[ dfb | <a href='http://www.epta.eu.org/roach'>roach</a> | <a href='http://www.epta.eu.org/cobra2'>cobra2</a> | <a href='http://www.epta.eu.org/transient'>transient</a> ]</p>\n";

// Buttons
echo "<table>\n<tr>\n<th>\n";
if ($psr!=NULL && $num>1) {
  echo "<a href='index.php?i=" . ($n-$num) . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=" . $size . "'><img src='images/skip-backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i+5*$num) . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=" . $size . "'><img src='images/fast-backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i+$num) . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=" . $size . "'><img src='images/backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i-$num) . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=" . $size . "'><img src='images/forward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i-5*$num) . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=" . $size . "'><img src='images/fast-forward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
 } else if ($psr!=NULL && $num==1) {
  echo "<a href='index.php?i=" . ($n-$num) . "&psr=" . urlencode($psr) . "&s=" . $size . "'><img src='images/skip-backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i+5*$num) . "&psr=" . urlencode($psr) . "&s=" . $size . "'><img src='images/fast-backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i+$num) . "&psr=" . urlencode($psr) . "&s=" . $size . "'><img src='images/backward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i-$num) . "&psr=" . urlencode($psr) . "&s=" . $size . "'><img src='images/forward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
  echo "<a href='index.php?i=" . ($i-5*$num) . "&psr=" . urlencode($psr) . "&s=" . $size . "'><img src='images/fast-forward-icon.png' border='0'></a>\n";
  echo "</th><th>\n";
} else {
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
 }
echo "<a href='index.php'><img src='images/skip-forward-icon.png' border='0'></a>\n";
echo "</th>\n</tr></table>\n";

// Form
echo "<p><form action='index.php' method='get'>";
echo "<input type='text' name='psr' />";
echo "<input type='submit' />";
echo "</form></p>\n";

// Selection
if ($psr) {
  echo "[ <a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=1&s=" . $size . "'>1</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=5&s=" . $size . "'>5</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=10&s=" . $size . "'>10</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=25&s=" . $size . "'>25</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=50&s=" . $size . "'>50</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=0'>L</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&psr=" . urlencode($psr) . "&n=" . $num . "&s=1'>S</a> ]\n";
 } else {
  echo "[ <a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=1&s=" . $size . "'>1</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=5&s=" . $size . "'>5</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=10&s=" . $size . "'>10</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=25&s=" . $size . "'>25</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=50&s=" . $size . "'>50</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=" . $num . "&s=0'>L</a> | \n";
  echo "<a href='http://www.epta.eu.org/dfb/index.php?i=" . $i . "&n=" . $num . "&s=1'>S</a> ]\n";
 }

// Keep index in range
if ($j<0)
  $j=0;
if ($j>$n-1)
  $j=$n-1;

// Loop over plots
for ($k=$j,$flag=0;$k>$j-$num;$k--) {
  list($date, $time, $name) = explode("_", $files[$k]);

  // Exit if out of array
  if (!$files[$k])
    break;

  // Plot information
  //  if ($flag==0) {
    $result=mysql_query("select * from pulsars where name like '" . $name . "'");
    while ($row=mysql_fetch_array($result)) {
      echo "<p>" . $files[$k] . "; <b>Pulsar:</b> " . $row['name'] . "; <b>Period:</b> " . $row['period'] . " s; <b>DM:</b> " . $row['dm'] . " pc cm<sup>-3</sup>";
      if ($row['is_binary']==1) {
	echo "; <b>Comments:</b> binary";
	if ($row['notes']!="")
	  echo ", ". $row['notes'];
      } else {
	if ($row['notes']!="")
	  echo "; <b>Comments:</b> ". $row['notes'];
      }
    }
    echo "</p>";
    //  }
  if ($psr)
    $flag=1;

  // Plots
  if ($size==0) {
    echo "<p><table cellpadding='0' cellspacing='0'>\n<tr>\n";
    echo "<td><a href='" . $files[$k] . "_sft.png'><img src=" . $files[$k] . "_sft.png width='425' height='340' border='0'></td>\n";
    echo "<td><a href='" . $files[$k] . "_gtp.png'><img src=" . $files[$k] . "_gtp.png width='425' height='340' border='0'></td>\n";
    echo "<td><a href='" . $files[$k] . "_yfp.png'><img src=" . $files[$k] . "_yfp.png width='425' height='340' border='0'></td>\n";
    echo "</tr>\n</table>\n";
  } else {
    echo "<p><table cellpadding='0' cellspacing='0'>\n<tr>\n";
    echo "<td><a href='" . $files[$k] . "_sft_s.png'><img src=" . $files[$k] . "_sft_s.png width='425' height='340' border='0'></td>\n";
    echo "<td><a href='" . $files[$k] . "_gtp_s.png'><img src=" . $files[$k] . "_gtp_s.png width='425' height='340' border='0'></td>\n";
    echo "<td><a href='" . $files[$k] . "_yfp_s.png'><img src=" . $files[$k] . "_yfp_s.png width='425' height='340' border='0'></td>\n";
    echo "</tr>\n</table>\n";
  }
  // Link
  echo "<a href='plot.php?fileroot=" . urlencode($files[$k]) . "'>Link to observation</a></p>\n";
 }

echo "</div></body>\n</html>\n";

mysql_close();

?>