<?php
/*
 * xyzanalytics - A small analytics script
 * Written in 2018 by cmp cmp@airmail.cc
 * To the extent possible under law, the author(s) have dedicated all copyright and related and neighboring rights to this software to the public domain worldwide. This software is distributed without any warranty.
 * You should have received a copy of the CC0 Public Domain Dedication along with this software. If not, see <http://creativecommons.org/publicdomain/zero/1.0/>.
 */

// ******** CONFIG ********
$MYSQL_USER = "analytics";  // MySQL user
$MYSQL_PASS = "pass";       // MySQL password
$MYSQL_DB = "analytics";    // MySQL database
$MYSQL_TB = "con";          // MySQL table
$GEO_REFRESH = 604800;      // Geolocation refresh rate (seconds)
// ************************

function find_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
       return $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        return $_SERVER['REMOTE_ADDR'];
    else
        return false;
}dont merge this i have no idea what im doing

function get_geolocation($ip) {
    return $ip === "unknown" ? "unknown" : json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
}

function DntCheck(): bool
{
   return (bool)$_SERVER['HTTP_DNT'] ?? false;
}

$sql = new mysqli("127.0.0.1", $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
if (!$sql) {
    echo '[A:1] MySQL error.';
    return;
}

$current_time = time();
$pretty_time = gmdate("Y-m-d H:i:s", $current_time);
$location = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$domain = $_SERVER['HTTP_HOST'];
$user_agent = !isset($_SERVER['HTTP_USER_AGENT']) ? "unknown" : $_SERVER['HTTP_USER_AGENT'];

if (DntCheck()) {
    $REDACT = true;
} else {
    $REDACT = false;
}

if ($REDACT) {
    $client_ip = "[REDACTED]";
} else {
    $client_ip = find_ip();
    $client_ip = $client_ip === false ? "unknown" : $client_ip;
}

$x = new stdClass();

$x->location = $sql->real_escape_string($location);
$x->ip = $sql->real_escape_string($client_ip);
$x->time = $sql->real_escape_string($current_time);
$x->pretty_time = $sql->real_escape_string($pretty_time);
$x->user_agent = $sql->real_escape_string($user_agent);
$x->domain = $sql->real_escape_string($domain);

if ($REDACT) {
    $x->geo->city = "[REDACTED]";
    $x->geo->region = "[REDACTED]";
    $x->geo->country = "[REDACTED]";
    $x->geo->location = "[REDACTED]";
    $x->geo->isp = "[REDACTED]";
    goto skip_geo;
}

$q = "SELECT * FROM {$MYSQL_TB} WHERE ip='{$x->ip}' ORDER BY id DESC LIMIT 1";
$res = $sql->query($q);
if (!$res) {
    echo '[A:2] MySQL error.';
    return;
} else {
    if ($res->num_rows === 0) {
        $geolocation = get_geolocation($client_ip);
    } else {
        $r = $res->fetch_assoc();
        if ($current_time - $r["time"] >= $GEO_REFRESH) {
            $geolocation = get_geolocation($client_ip);
        } else {
            $geolocation = new stdClass();
            $geolocation->city = $r["geo_city"];
            $geolocation->region = $r["geo_region"];
            $geolocation->country = $r["geo_country"];
            $geolocation->loc = $r["geo_location"];
            $geolocation->org = $r["geo_isp"];
        }
    }
}

if ($geolocation !== "unknown" && $client_ip !== "127.0.0.1") {
    $x->geo->city = $sql->real_escape_string($geolocation->city);
    $x->geo->region = $sql->real_escape_string($geolocation->region);
    $x->geo->country = $sql->real_escape_string($geolocation->country);
    $x->geo->location = $sql->real_escape_string($geolocation->loc);
    $x->geo->isp = $sql->real_escape_string($geolocation->org);
} else {
    $x->geo->city = "unknown";
    $x->geo->region = "unknown";
    $x->geo->country = "unknown";
    $x->geo->location = "unknown";
    $x->geo->isp = "unknown";
}

skip_geo:

$q = "INSERT INTO {$MYSQL_TB}(`ip`, `time`, `pretty_time`, `user_agent`, `location`, `domain`, `geo_city`, `geo_region`, `geo_country`, `geo_location`, `geo_isp`) VALUES('{$x->ip}', '{$x->time}', '{$x->pretty_time}', '{$x->user_agent}', '{$x->location}', '{$x->domain}', '{$x->geo->city}', '{$x->geo->region}', '{$x->geo->country}', '{$x->geo->location}', '{$x->geo->isp}')";
$r = $sql->query($q);
if (!$r) {
    echo '[A:3] MySQL error.';
    return;
}

$sql->close();

?>
