# xyzanalytics
A small (under 100 lines) PHP script for collecting analytics. It collects IP addresses, so, it's your duty to be GDPR-compliant here.

## Usage
The repository has two scripts, let's break down their usage.

### include.php
This is the file that you include in other PHP files to collect usage data.  

#### Configuration
```
$MYSQL_USER = "analytics";  // MySQL user
$MYSQL_PASS = "password";   // MySQL password
$MYSQL_DB = "analytics";    // MySQL database
$MYSQL_TB = "con";          // MySQL table
$GEOLOC = true;             // Enable geolocation detection
```
Change these values with what you set up with on the server.

#### Usage
Append the following string in your PHP scripts:
```php
include('include.php');
```
Please note that you ~~can~~ should replace the `include.php` to another value where the script will be stored, such as:
```php
include('/home/www-data/include.php');
```
And that's it!

### viewer.php
Because the script uses SQL, you need to know how to use MySQL to make any use of your data. If you're a data scientist - that may be a problem, but if you just want to look at your visits - this is enough. This is a very basic script to let you view collected data easily.

#### Configuration
```
$SECRET_KEY = "secretkey";             // Secret access key
$USER = "analytics";                   // MySQL user
$PASS = "pass";                        // MySQL password
$DB = "analytics";                     // MySQL database
$TB = "con";                           // MySQL table
$GEO = true;                           // Enable geolocation viewing
```
The secret key has to be a SHA256 hashed string, this expects a `hash()`ed string, to get one, you can use [this tool](https://f00f.xyz/tools/sha256.php) or [phpsh](http://www.phpsh.org/), then use as follows:
```
php> var_dump(hash('sha256', "<enter your secret here>"));
string(64) "4d9be233b02bff1cb0063c62bb8f02f56c75ee5061eea337ceeac0d16a0cffba"
php>
```
Copy what's in quotes.

#### Usage
Open it from a browser

#### Javascript (script.js)
Javascript is stored in a file - `script.js`. From the PHP script you can change its name. Or not.

## Requirements
| PHP modules |
| ----------- |
| php-mysqli  |
| php-json    |

| SQL server |
| ---------- |
| MySQL      |
| *or*       |
| MariaDB    |

## Setup
| File          | Location                            | Notes                      | Required                 |
| ------------- | ----------------------------------- | -------------------------- | ------------------------ |
| `include.php` | Preferably outside the webroot      | The main script to include | Yes                      |
| `viewer.php`  | Inside webroot, viewable by browser | Viewer script              | No                       |
| `script.js`   | Inside webroot, viewable by browser | For viewer script          | If viewer is set up, yes |

## SQL setup
* Make a database for this
* Set up an account and give permissions to it
* Create the following table
```
create table con(`id` int not null auto_increment, `ip` varchar(64) not null, `time` int not null, `pretty_time` varchar(128) not null `location` varchar(128) not null, `domain` varchar(128) not null, `geo_city` varchar(1024), `geo_region` varchar(1024), `geo_country` varchar(1024), `geo_location` varchar(1024), `geo_isp` varchar(1024), primary key(`id`) );
```

## API
For geolocation, this script uses the ipinfo.io API. It's **free for the first 1000 requests per day**. If you think your service will expect more traffic than that, you can [buy a plan](https://ipinfo.io/pricing), disable the geolocation detection, look for another analytics solution or replace it with another API.

## Third party projects used
|   Name        | Where         | Link              |
| ------------- | ------------- | ----------------- |
| ipinfo.io API | `include.php` | https://ipinfo.io |

## What's up with the indentation?
I use more than one editor. Sometimes configurations are screwed up and it uses tabs instead of spaces and vice versa. If it *works on my machine*, I won't fix it until it becomes a problem.
