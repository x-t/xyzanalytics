<!DOCTYPE html>
<html>
<head>
<?php
/*
 * xyzanalytics - A small analytics script
 * Written in 2018 by cmp cmp@airmail.cc
 * To the extent possible under law, the author(s) have dedicated all copyright and related and neighboring rights to this software to the public domain worldwide. This software is distributed without any warranty.
 * You should have received a copy of the CC0 Public Domain Dedication along with this software. If not, see <http://creativecommons.org/publicdomain/zero/1.0/>.
 */

// ******** CONFIG ********
	$SECRET_KEY = "sha256 hashed secret key";
	$USER = "analytics";
	$PASS = "pass";
	$DB = "analytics";
    $TB = "con";
    $GEO = true;
// ************************
	?>
	<title>xyznalytics viewer</title>
	<style>
	body { font-family: Arial, Helvetica, sans-serif; }
    .c { text-align: center; }
    table {
        border-collapse: collapse;
        margin: 0 auto;
    }

    table, th, td {
        border: 2px solid black;
        padding: 5px;
    }
	</style>
</head>
<body>
<?php

if (isset($_POST["key"])) {
	$key = hash('sha256', $_POST["key"]);
	if ($key !== $SECRET_KEY) {
		echo "Incorrect key.";
		exit();
	}
	echo '<script src="script.js"></script>';
	echo '<p id="filtDom">Filter domain: <input id="site" type="text"><button type="button" onclick="filterSite()">Filter</button></p>';
    echo '<p id="filtDomA"></p>';
    if ($GEO === true) {
        echo '<p><input type="checkbox" id="filtBot" onchange="hideBots()"> Hide bots</p>';
    }
	$sql = new mysqli("127.0.0.1", $USER, $PASS, $DB);
	if (!$sql) {
		echo 'MySQL error';
		exit();
	}
	$q = "SELECT * FROM {$TB}";
	$res = $sql->query($q);
	if (!$res) {
		echo 'MySQL error';
		exit();
	}
	if ($res->num_rows == 0) {
		echo 'Nothing here.';
		exit();
	}
	$x = new stdClass();
	while ($r = $res->fetch_assoc()) {
		$x->col[$r["id"] - 1]->ip = $r["ip"];
		$x->col[$r["id"] - 1]->time = $r["time"];
		$x->col[$r["id"] - 1]->pretty_time = $r["pretty_time"];
		$x->col[$r["id"] - 1]->location = $r["location"];
        $x->col[$r["id"] - 1]->domain = $r["domain"];
        if ($GEO === true) {
		    $x->col[$r["id"] - 1]->geo_city = $r["geo_city"];
		    $x->col[$r["id"] - 1]->geo_region = $r["geo_region"];
		    $x->col[$r["id"] - 1]->geo_country = $r["geo_country"];
		    $x->col[$r["id"] - 1]->geo_location = $r["geo_location"];
            $x->col[$r["id"] - 1]->geo_isp = $r["geo_isp"];
        }
		$x->col[$r["id"] - 1]->user_agent = $r["user_agent"];
	}
	echo "<table>";
		echo '<tr>';
			echo '<th>IP</th>';
			echo '<th>Unix Time</th>';
			echo '<th>Pretty Time</th>';
			echo '<th>Location</th>';
            echo '<th>Domain</th>';
    if ($GEO === true) {
			echo '<th>Geo:City</th>';
			echo '<th>Geo:Region</th>';
			echo '<th>Geo:Country</th>';
			echo '<th>Geo:Location</th>';
            echo '<th>Geo:ISP</th>';
    }
			echo '<th>User Agent</th>';
		echo '</tr>';
        for ($i = 1; $i <= sizeof($x->col); $i++) {
			echo '<tr>';
				echo '<td>' . htmlspecialchars($x->col[$i]->ip) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->time) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->pretty_time) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->location) . '</td>';
                echo '<td data-domain="1">' . htmlspecialchars($x->col[$i]->domain) . '</td>';
            if ($GEO === true) {
				echo '<td>' . htmlspecialchars($x->col[$i]->geo_city) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->geo_region) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->geo_country) . '</td>';
				echo '<td>' . htmlspecialchars($x->col[$i]->geo_location) . '</td>';
                echo '<td data-isp="1">' . htmlspecialchars($x->col[$i]->geo_isp) . '</td>';
            }
				echo '<td>' . htmlspecialchars($x->col[$i]->user_agent) . '</td>';
			echo '</tr>';
		}
	echo "</table>";

	exit();
}

?>
	<div class="c">
		<h3>xyzanalytics</h3>
		<form action="" method="post">
			<input type="text" placeholder="Access key" name="key">
			<input type="submit" value="Login">
		</form>
		<br>
	</div>
</body>
</html>
