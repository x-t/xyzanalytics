<?php

// ******** CONFIG ********
$MYSQL_USER = "analytics";  // MySQL user
$MYSQL_PASS = "pass";       // MySQL password
$MYSQL_DB = "analytics";    // MySQL database
$MYSQL_TB = "con";          // MySQL table
$GEOLOC = true;             // Enable geolocation detection
// ************************

function find_ip() {
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
       $ip = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = false;
    return $ip;
}

$sql = new mysqli("127.0.0.1", $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);
if (!$sql) {
    echo 'MySQL error.';
    return;
}

$client_ip = find_ip() === false ? "unknown" : find_ip();
$current_time = time();
$pretty_time = gmdate("Y-m-d H:i:s", $current_time);
$user_agent = !isset($_SERVER['HTTP_USER_AGENT']) ? "unknown" : $_SERVER['HTTP_USER_AGENT'];
$location = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$domain = $_SERVER['HTTP_HOST'];
if ($GEOLOC === true) {
    $geolocation = $client_ip === "unknown" ? "unknown" :
                   json_decode(file_get_contents("https://ipinfo.io/{$client_ip}/json"));
}

$x = new stdClass();

$x->location = $sql->real_escape_string($location);
$x->ip = $sql->real_escape_string($client_ip);
$x->time = $sql->real_escape_string($current_time);
$x->pretty_time = $sql->real_escape_string($pretty_time);
$x->user_agent = $sql->real_escape_string($user_agent);
$x->domain = $sql->real_escape_string($domain);
if ($GEOLOC !== false) {
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
}

$q = "INSERT INTO {$MYSQL_TB}(`ip`, `time`, `pretty_time`, `user_agent`, `location`, `domain`, `geo_city`, `geo_region`, `geo_country`, `geo_location`, `geo_isp`) VALUES('{$x->ip}', '{$x->time}', '{$x->pretty_time}', '{$x->user_agent}', '{$x->location}', '{$x->domain}', '{$x->geo->city}', '{$x->geo->region}', '{$x->geo->country}', '{$x->geo->location}', '{$x->geo->isp}')";
$r = $sql->query($q);
if (!$r) {
    echo 'MySQL error.';
    return;
}
$sql->close();

?>
