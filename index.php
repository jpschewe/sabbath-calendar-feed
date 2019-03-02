<?php
/**
* @desc simple! Sabbath iCalendar generator to be used in Google Calendar, your phone and elsewhere
* @since 2015-04-12
* @author Jon Schewe <jpschewe@mtu.net> based on code from Allan Laal <allan@permanent.ee>
* @example http://sun.is.permanent.ee/?title=sunrise,sunset,length&label_sunrise=↑&label_sunset=↓&start=-100&end=365
* @link https://github.com/jpschewe/sabbath-calendar-feed
*/
$version = '20190302T170500Z'; // modify this when you make changes in the code!

// include config:
require_once('./config.php');

// get and set timezone:
$latitude = $config['latitude'];
$longitude = $config['longitude'];

$timezoneId = $config['timezone'];

date_default_timezone_set($timezoneId);


// buffer output so if anything fails, it wont display a partial calendar
$out = "BEGIN:VCALENDAR\r\n";
$out .= "PRODID:-//Schewe Consultants//Sabbath Calendar//EN\r\n";
$out .= "VERSION:5.1.4\r\n";
$out .= "CALSCALE:GREGORIAN\r\n";
$out .= "METHOD:PUBLISH\r\n";
$out .= "X-WR-TIMEZONE:".$timezoneId."\r\n";
$out .= "URL:https://github.com/jpschewe/sabbath-calendar-feed\r\n";
$out .= "X-WR-CALNAME:Sabbath\r\n";
$out .= "X-WR-CALDESC:Display begin and end of Sabbath from a constantly updating vcalendar/ICS calendar in Google Calendar, your phone or elsewhere.\r\n";
$out .= "X-LOTUS-CHARSET:UTF-8\r\n";

//$out .= "X-PUBLISHED-TTL:".(30*24*60*60)."\r\n"; // check back in 1 month
//$out .= "REFRESH-INTERVAL\r\n";

$now = date('Y-m-d', time());
for ($day=param('start', 0); $day<=param('end', 365); $day++)
{
  $current_timestamp = strtotime($now.' +'.$day.' days');
  $day_of_week = date('w', $current_timestamp);
  $current = date('Ymd', $current_timestamp);

  if($day_of_week == 5 || $day_of_week == 6) {
    $sun_info = date_sun_info($current_timestamp, $latitude, $longitude);

    $start_timestamp = $sun_info['sunset'];
    // add 60 seconds to make the end 1 minute after the start
    $end_timestamp = $start_timestamp + 60;

    // Need to set the timezone to UTC to ensure that it is properly added to the calendar
    date_default_timezone_set('UTC');
    $start = date('Ymd\THis\Z', $start_timestamp);
    $end = date('Ymd\THis\Z', $end_timestamp);
    date_default_timezone_set($timezoneId);

    $out .= "BEGIN:VEVENT\r\n";
    $out .= "DTSTART:".$start."\r\n";
    $out .= "DTEND:".$end."\r\n";
    $out .= "DTSTAMP:".$start."\r\n";
    $out .= "UID:Permanent-Sabbath-".$current."-$version\r\n";
    $out .= "CLASS:PUBLIC\r\n";
    $out .= "CREATED:$version\r\n";
    $out .= "GEO:$latitude;$longitude\r\n"; //@see http://tools.ietf.org/html/rfc2445

    $out .= 'DESCRIPTION:'.'Sunset is at '.date('g:i a', $sun_info['sunset'])."\r\n";

    $out .= "LAST-MODIFIED:$version\r\n";
    $out .= "SEQUENCE:0\r\n";
    $out .= "STATUS:CONFIRMED\r\n";

    $out .= "SUMMARY:".'Sunset is at '.date('g:i a', $sun_info['sunset'])."\r\n";

    $out .= "TRANSP:OPAQUE\r\n";
    $out .= "END:VEVENT\r\n";
  } // if Friday or Saturday

} // foreach day

$out .= 'END:VCALENDAR';


header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename='.param('filename'));
echo $out;


/**
* @param int $sunset
* @param int $sunrise
* @return string
* @example 14h28
*/
function calc_day_length($sunset, $sunrise)
{
  $day_length = $sunset - $sunrise;
  $day_length_h = intval($day_length/60/60);
  $day_length_min = round(($day_length - $day_length_h*60*60)/60, 0);
  $length = "{$day_length_h}h".str_pad($day_length_min, 2, '0', STR_PAD_LEFT);

  return $length;
}


/**
* @param string $name
* @param string $default
* @return string
* @desc GET an URL parameter
*/
function param($name, $default='')
{
  //	echo "&$name=$default"; // builds URL parameters with the default values

  if (
    isset($_GET[$name])
    &&
    !empty($_GET[$name])
    )
    {
      $out = filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
    }
    else
    {
      $out = $default;
    }

    return $out;
  }
