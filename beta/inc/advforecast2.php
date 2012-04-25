<?php
// error_reporting(E_ALL);
//This is a more advanced version of the forecast script
//It uses file caching and feed failure to better handle when NOAA is down
//
//  Version 2.00 - 15-Jan-2007 - modified Tom's script for XHTML 1.0-Strict
//  Version 2.01 - 14-Feb-2007 - modified for ERH->CRH.noaa.gov redirects (no point forecasts)
//  Version 2.02 - 02-Mar-2007 - added auto-failover to CRH and better include in page.
//  Version 2.03 - 29-Apr-2007 - modified for /images/wtf -> /forecast/images change
//  Version 2.04 - 05-Jun-2007 - improvement to auto-failover
//  Version 2.05 - 29-Jun-2007 - additional check for alternative no-icon forecast, then failover.
//  Version 2.06 - 24-Nov-2007 - rewrite for zone forecast, intrepret icons from text-only forecast
//  Version 2.07 - 24-Nov-2007 - support new zone forecast with different temp formats
//  Version 2.08 - 25-Nov-2007 - fix zone forecast icons, new temp formats supported
//  Version 2.09 - 26-Nov-2007 - add support for new temperature phrases and below zero temps
//  Version 2.10 - 17-Dec-2007 - added safety features from Mike Challis http://www.carmosaic.com/weather/
//  Version 2.11 - 20-Dec-2007 - added cache-refresh on request, fixed rising/falling temp arrow
//  Version 2.12 - 31-Dec-2007 - fixed New Year"s to New Year's display problem
//  Version 2.13 - 01-Jan-2008 - added integration features for carterlake/WD/PHP/AJAX template set
//  Version 2.14 - 14-Jan-2009 - corrected Zone forecast parsing for below zero temperatures
//  Version 2.15 - 28-Feb-2010 - updated Zone forecast parsing for new phrases
//
$Version = 'advforecast2.php - V2.15 - 28-Feb-2010';
//
//import NOAA Forecast info
//data ends up in four different arrays:
//$forecasticons[x]  x = 0 thru 9   This is the icon and text around it
//$forecasttemp[x] x= 0 thru 9    This is forecast temperature with styling
//$forecasttitles[x]  x = 0 thru 12   This is the title word for the text forecast time period
//$forecasttext[x]  x = 0 thru 12  This is the detail text for the text forecast time period
//
//$forecastupdate  This is the time of last update
//$forecastcity    This is the city name for the forecast
//$forecastoffice  This is the NWS Office providing the forecast
//
//Also, in order for this to work correctly, you need the NOAA icons (or make your own...
//there are over 200!). These need to be placed in the path where the original NOAA icons
//are located. In my case, they are at: \forecast\images\
//properly (so make a folder in your web HTML root called "forecast", then make a folder in it
//called "images", and place the icons in this folder)
//
//http://members.cox.net/carterlakeweather/forecasticons.zip (380K)
//
//URL below --MUST BE-- the Printable Point Forecast from the NOAA website
//
//Not every area of the US has a printable point forecast
//
//This script will ONLY WORK with a printable point forecast!
//
//To find yours in your area:
//
//Go to www.weather.gov
//Put your city, state in the search box and press Search
//Scroll down to the "Additional Forecasts & Info" on the page displayed
//Click on Printable Forecast
// copy the URL from your browser into the $fileName variable below.
// Also put your NOAA Warning Zone (like ssZnnn) in caps in the $NOAAZone variable below.
//
// also set your NOAA warning zone here to use for automatic backup in case
// the point printable forecast is not available.
//
// ----------------------SETTINGS---------------------------------------------
//
$NOAAZone = 'CAZ513';  // change this line to your NOAA warning zone.
// set $fileName to the URL for the point-printable forecast for your area
$fileName = "http://forecast.weather.gov/MapClick.php?site=mtr&smap=1&textField1=37.26389&textField2=-122.02194&TextType=2";//
$iconDir = './forecast/images/';
// ----------------------END OF SETTINGS--------------------------------------
// Get the forecast.txt file or a new one from NOAA
// You have to have a forecast.txt in place for this script to work
// You can see ours at http://www.carterlake.org/forecast.txt
//

// overrides from Settings.php if available
global $SITE;
if (isset($SITE['noaazone'])) 	{$NOAAZone = $SITE['noaazone'];}
if (isset($SITE['fcsturlNWS'])) 	{$fileName = $SITE['fcsturlNWS'];}
if (isset($SITE['fcsticonsdir'])) 	{$iconDir = $SITE['fcsticonsdir'];}
// end of overrides from Settings.php


// This is version 1.2 with Ken's modifications from Saratoga Weather
// http://saratoga-weather.org/

// You can now force the cache to update by adding ?force=1 to the end of the URL

if ( empty($_REQUEST['force']) )
        $_REQUEST['force']="0";

$Force = $_REQUEST['force'];

$forceBackup = false;
if ($Force > 1) {$forceBackup = true; }

$cacheName = "forecast.txt"; 

// dont change the next line....
$backupfileName = "http://forecast.weather.gov/MapClick.php?zoneid=$NOAAZone";

$Status = "<!-- $Version -->\n<!-- NWS URL: $fileName -->\n<!-- zone=$NOAAZone -->\n";

if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');

   readfile($filenameReal);
   exit;
}
$fcstPeriods = array( // for filling in the '<period> Through <period>' zone forecasts.
'Monday','Monday Night',
'Tuesday','Tuesday Night',
'Wednesday','Wednesday Night',
'Thursday','Thursday Night',
'Friday','Friday Night',
'Saturday','Saturday Night',
'Sunday','Sunday Night',
'Monday','Monday Night',
'Tuesday','Tuesday Night',
'Wednesday','Wednesday Night',
'Thursday','Thursday Night',
'Friday','Friday Night',
'Saturday','Saturday Night',
'Sunday','Sunday Night'
);

$usingFile = "";

if ($Force==1) {
      $html = fetchUrlWithoutHanging($fileName,$cacheName);
      if (preg_match('/Temporary|Location:|defaulting to/Uis',$html)) {
         $usingFile = "(Zone forecast)";
         $html = fetchUrlWithoutHanging($backupfileName,$cacheName);
      }
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
            $Status .= "<!-- unable to write cache file $cacheName -->\n";
      }
  }

if ($Force==2) {
      $html = fetchUrlWithoutHanging($backupfileName,$cacheName);
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
            $Status .= "<!-- unable to write cache file $cacheName -->\n";
      }
      $usingFile = "(Zone forecast)";
  }

// The number 1800 below is the number of seconds the cache will be used instead of pulling a new file
// 1800 = 60s x 30m so it retreives every 30 minutes.

if (filemtime($cacheName) + 1800 > time()) {
      $Status .= "<!-- loading $cacheName -->\n";
      $html = implode('', file($cacheName));
      if (preg_match('/Temporary|Location:|defaulting to/is',$html)) {
         $usingFile = "(Zone forecast)";
         $html = fetchUrlWithoutHanging($backupfileName,$cacheName);
      }
    } else {
      $html = fetchUrlWithoutHanging($fileName,$cacheName);
      if (preg_match('/Temporary|Location:|defaulting to/Uis',$html)) {
         $usingFile = "(Zone forecast)";
         $html = fetchUrlWithoutHanging($backupfileName,$cacheName);
      }
      $fp = fopen($cacheName, "w");
      if ($fp) {
        $write = fputs($fp, $html);
        fclose($fp);
      } else {
        $Status .= "<!-- unable to write cache file $cacheName -->\n";
      }
}

if (isset($_REQUEST['test'])) {
  $tfile = "./forecast-" . trim($_REQUEST['test']) . '.txt';
  if(file_exists($tfile)) {
    $Status .= "<!-- using $tfile for testing -->\n";
    $html = implode('',file($tfile));
  } else {
    $Status .= "<!-- unable to locate $tfile for testing -->\n";
  }
}

$isZone = preg_match('|<b>Zone Forecast: </b>|i',$html); // here with Zone forecast sans icons

if ($isZone) { // using the zone forecast
        $usingFile = "(Zone forecast)";
        $Conditions = array();  // prepare for parsing the icon based on the text forecast
        load_cond_data(); // initialize the conditions to look for
        preg_match(
         '|<a name="contents"></a>(.*)<td valign="top" align="center" width="50%">|Uis',
        $html, $betweenspan);
//           $Status .= "<!-- betweenspan \n" . print_r($betweenspan,true) . "-->\n";
        $forecastop = $betweenspan[1];

//     slice off the text forecast from the Zone forecast
        preg_match_all("|<b>(.*)</b>\.\.\.(.*)<br><br>|Uis", $forecastop, $headers);
        $forecaststuff = $headers[1];
//           $Status .= "<!-- headers \n" . print_r($headers,true) . "-->\n";

//     Breakup multi-day forecasts if needed
        $i = 0;
    foreach ($headers[1] as $j => $period) {
      if (preg_match('/^(.*) (Through|And) (.*)/i',$period,$mtemp)) { // got period1 thru period2
                list($fcstLow,$fcstHigh) = explode("\t",split_fcst($headers[2][$j]));
                $startPeriod = $mtemp[1];
                $periodType = $mtemp[2];
                $endPeriod = $mtemp[3];
                $startIndex = 0;
                $endIndex = 0;
                $Status .= "<!-- splitting $periodType '$period'='" . $headers[2][$j] . "' -->\n";
                for ($k=0;$k<count($fcstPeriods);$k++) { // find Starting and ending period indices
                  if(!$startIndex and $startPeriod == $fcstPeriods[$k] ) {
                         $startIndex = $k;
                  }
                  if($startIndex and !$endIndex and $endPeriod == $fcstPeriods[$k]) {
                        $endIndex= $k;
                        break;
                  }
                 }

                for ($k=$startIndex;$k<=$endIndex;$k++) { // now generate the period names and appropriate fcst
                  if(preg_match('|night|i',$fcstPeriods[$k])) {
                        $forecasttext[$i] = $fcstLow;
                   } else {
                        $forecasttext[$i] = $fcstHigh;
                   }
                   $forecasttitles[$i] = $fcstPeriods[$k];
                   $Status .= "<!-- $periodType $j, $i, '" .
                                          $forecasttitles[$i] . "'='" . $forecasttext[$i] . "' -->\n";
                   $i++;
                 }
           continue;
         }

         $forecasttitles[$i] = $period;
         $forecasttext[$i] = $headers[2][$j];
         $Status .= "<!-- normal $j, $i, '" . $forecasttitles[$i] . "'='" . $forecasttext[$i] . "' -->\n";
         $i++;

   } // end of multi-day forecast split

   for ($i=0;$i<=min(8,count($headers[1])-1);$i++) { // intrepet the text for icons, summary, temp, PoP
          list($forecasticons[$i],$forecasttemp[$i],$forecastpop[$i]) =
                explode("\t",make_icon($forecasttitles[$i],$forecasttext[$i]) );
   }

 } else { // original format point printable forecast

      preg_match('|<tr valign ="top" align="center">(.*)<table width="670"|s', $html, $betweenspan);
      $forecastop  = $betweenspan[1];

      // Chop up each icon html and place in array
      preg_match_all("/<td.*>(.*)<\/td>/Uis", $forecastop, $headers);
      $forecasticons = $headers[1];

          }

// saratoga-weather.org mod: fix up html for XHTML 1.0-Strict
//     $Status .= "<!-- \n" . print_r($forecasticons,true) . "-->\n";
                for ($i=0;$i<count($forecasticons);$i++) {
                   $forecasticons[$i] = preg_replace('|/images/wtf|Uis',
                   '/forecast/images',$forecasticons[$i]);
                    $forecasticons[$i] = preg_replace('|"images/|Uis',
                   '"/forecast/images/',$forecasticons[$i]);
                  $forecasticons[$i] = preg_replace('|/forecast/images/|Uis',
                   $iconDir,$forecasticons[$i]);
                   $forecasticons[$i] = preg_replace('|<br><br>\s+$|is',
                    "",$forecasticons[$i]);
//                   $forecasticons[$i] = preg_replace('|temperatures|is','temps',$forecasticons[$i]);
//                   $forecasticons[$i] = preg_replace('|Falling|is',' Falling',$forecasticons[$i]);
//                   $forecasticons[$i] = preg_replace('|\'|is','"',$forecasticons[$i]);  // change all ' to "
                   $forecasticons[$i] = preg_replace('|\\\\" ><br>|is','" /><br />',$forecasticons[$i]);
                   $forecasticons[$i] = preg_replace('|<font color="(.*)\">(.*)</font>|Uis',
                     "<span style=\"color: $1;\">$2</span>",$forecasticons[$i]);
                   preg_match_all('|<br>([^<]+)(<span.*</span>.*)|is',$forecasticons[$i],$matches);
//                   $Status .= "<!-- matches\n ".print_r($matches,true). "-->\n";
           if(isset($matches[2][0]) and preg_match('|<img .*>|i',$matches[2][0])) {
                     $t = $matches[2][0];
                         $t = preg_replace('|<img src="([^"]+)" (border="0") alt="([^"]+)">|i',
                          "<img src=\"\\1\" style=\"border: none;\" alt=\"\\3\" title=\"\\3\" />",$t);
                         $matches[2][0] = $t;
                   }
                   if (! $isZone) {
                     $forecasttemp[$i] = $matches[1][0] . $matches[2][0]; // just the temp line
                     # mchallis added security feature
                     $forecasttemp[$i] = strip_tags($forecasttemp[$i], '<b><br><br/><img><span>');
                   }
                   // remove the temp from the forecasticons
                   if(isset($matches[0][0])) {
                     $forecasticons[$i] = preg_replace('|'.$matches[0][0].'|is','',$forecasticons[$i]);
                   }
                   // fix up the <br> to be <br /> for XHTML compatibility
                   $forecasticons[$i] = preg_replace('|<br>|Uis','<br />',$forecasticons[$i]);
                     # mchallis added security feature
                     $forecasticons[$i] = strip_tags($forecasticons[$i], '<b><br><br/><img><span>');
//                   $forecasttemp[$i] = preg_replace('|<br>|Uis','<br />',$forecasttemp[$i]);
//                   $forecasttemp[$i] = trim($forecasttemp[$i]);
                }

//          $Status .= "<!-- \n" . print_r($forecasticons,true) . "-->\n";

// end saratoga-weather.org XHTML 1.0-Strict mod

      if ($isZone) { // special handling for ERH->CRH redirection

      // Grab the Last Update date and time.
      preg_match('|<b>Last Update:</b></a>(.*)<br></td>|', $html, $betweenspan);
      $forecastupdated  = $betweenspan[1];
      # mchallis added security feature
      $forecastupdated = strip_tags($forecastupdated, '<b><br><img><span>');
// saratoga-weather.org mod:
          // Grab the NWS Forecast for (city name)
          preg_match('|class="white1">\s*(.*)<a href|',$html,$betweenspan);
          $forecastcity  = $betweenspan[1];
          # mchallis added security feature
          $forecastcity = strip_tags($forecastcity, '<b><br><img><span>');
          // Grab the Issued by office
          preg_match('|<a href=http[^>]+>(.*)</a><br><b>Zone Forecast:|is',$html,$betweenspan);
          $forecastoffice  = trim($betweenspan[1]);
          $forecastoffice = preg_replace('|<br>|s','',$forecastoffice);


          } else { // begin regular handling

      // Now get just the bottom of the NWS page for editing
      preg_match('|</table>(.*)<hr>|s', $html, $betweenspan);
      $forecast  = $betweenspan[1];
      # mchallis added security feature
      $forecast = strip_tags($forecast, '<b><br><img><span>');

      // Chop up each title text and place in array
      preg_match_all('|<b>(.*): </b>|Ui', $forecast, $headers);
      $forecasttitles = $headers[1];

      // Chop up each forecast text and place in array
      preg_match_all('|</b>(.*)<br>|Ui', $forecast, $headers);
      $forecasttext = $headers[1];

      # BOF mchallis added security feature
      for ($i=0;$i<count($forecasttitles);$i++) {
        $forecasttitles[$i] = strip_tags($forecasttitles[$i], '<b><br><img><span>');
        $forecasttext[$i]   = strip_tags($forecasttext[$i], '<b><br><img><span>');
      }
      # EOF mchallis added security feature

      // Grab the Last Update date and time.
      preg_match('|Last Update: (.*)|', $html, $betweenspan);
      $forecastupdated  = $betweenspan[1];
          $forecastupdated  = preg_replace('|<[^>]+>|Uis','',$forecastupdated); // remove html markup


// saratoga-weather.org mod:
          // Grab the NWS Forecast for (city name)
          preg_match('|<b>NWS Forecast for: (.*)</b>|',$html,$betweenspan);
          $forecastcity  = $betweenspan[1];
          # mchallis added security feature
          $forecastcity = strip_tags($forecastcity, '<b><br><img><span>');

          // Grab the Issued by office
          preg_match('|Issued by: (.*)<br>|',$html,$betweenspan);
          $forecastoffice  = $betweenspan[1];
          # mchallis added security feature
          $forecastoffice = strip_tags($forecastoffice, '<b><br><img><span>');

          } // end regular handling

  $IncludeMode = false;
  $PrintMode = true;

  if (isset($doPrintNWS) && ! $doPrintNWS ) {
      return;
  }
  if (isset($_REQUEST['inc']) &&
      strtolower($_REQUEST['inc']) == 'noprint' ) {
          return;
  }

if (isset($_REQUEST['inc']) && strtolower($_REQUEST['inc']) == 'y') {
  $IncludeMode = true;
}
if (isset($doIncludeNWS)) {
  $IncludeMode = $doIncludeNWS;
}

// end saratoga-weather.org mod

//------------------------------------------------------------------------------------------
function fetchUrlWithoutHanging($url,$cacheurl)
   {
   global $Status;
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen

   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection)
       {
       $Status .= "<!-- Network error: $errstr ($errno) -->";
       $html = implode('', file($cacheurl));
       return($html);

       // You may wish to remove the following debugging line on a live Web site
       }    // end if
   else    {
       $xml = '';
       fputs($socketConnection, "GET $resourcePath HTTP/1.0\r\nHost: $domain\r\nCache-Control: no-cache, must-revalidate\r\nCache-control: max-age=0\r\n\r\n");

       // Loop until end of file
       while (!feof($socketConnection))
           {
           $xml .= fgets($socketConnection, 4096);
           }    // end while

       fclose ($socketConnection);

       }    // end else

   return($xml);

   }    // end fetchUrlWithoutHanging function
//------------------------------------------------------------------------------------------


// split off Low and High from multiday forecast
function split_fcst($fcst) {

  global $Status;

  $f = explode(". ",$fcst . ' ');
  $lowpart = 0;
  $highpart = 0;
  foreach ($f as $n => $part) {  // find the Low and High sentences
    if(preg_match('/Low/i',$part)) { $lowpart = $n; }
        if(preg_match('/High/i',$part)) { $highpart = $n; }
  }

  $f[$lowpart] = preg_replace('|(\d+) below|s',"-$1",$f[$lowpart]);
  $f[$lowpart] = preg_replace('/( above| below| zero)/s','',$f[$lowpart]);
  $f[$lowpart] .= '.';

  $f[$highpart] = preg_replace('|(\d+) below|s',"-$1",$f[$highpart]);
  $f[$highpart] = preg_replace('/( above| below| zero)/s','',$f[$highpart]);
  $f[$highpart] .= '.';

  $replpart = min($lowpart,$highpart)-1;

  $fcststr = '';

  for ($i=0;$i<=$replpart;$i++) {$fcststr .= $f[$i] . '. '; } // generate static fcst text

  $fcstLow = $fcststr . ' ' . $f[$lowpart];
  $fcstHigh = $fcststr . ' ' . $f[$highpart];

  return("$fcstLow\t$fcstHigh");

}
//------------------------------------------------------------------------------------------

// function make_icon: parse text and find suitable icon from zone forecast text for period

function make_icon($day,$textforecast) {
  global $Conditions,$Status;
  if (preg_match('| |i',$day) ) {
    $icon = "<b>" . preg_replace('| |','<br/>',$day,1) . '</b><br/>';
  } else {
    $icon = "<b>" . $day . '</b><br/><br/>';
  }
  $temperature = 'n/a';
  $pop = '';
  $iconimage = 'na.jpg';
  $condition = 'N/A';

  if (preg_match('|(\S+) (\d+) percent|',$textforecast,$mtemp)) { // found a PoP
//    $Status .= "<!-- chance of '" . $mtemp[1] . "'='" . $mtemp[2] . "' -->\n";
    $pop = $mtemp[2];
  }
  // handle negative temperatures in zone forecast
  $textforecast = preg_replace('/([\d|-]+) below/i',"-$1",$textforecast);
  $textforecast = preg_replace('/zero/','0',$textforecast);
  
  if (preg_match('/(Highs|Lows|Temperatures nearly steady|Temperatures falling to|Temperatures rising to|Near steady temperature) (in the upper|in the lower|in the mid|in the low to mid|in the lower to mid|in the mid to upper|in the|around|near|nearly|above|below|from) ([\d|-]+)/i',$textforecast,$mtemp)) { // found temp
    //$Status .= "<!-- temp " . print_r($mtemp,true) . " -->\n";
        if (substr($mtemp[1],0,1) == 'T' or substr($mtemp[1],0,1) == 'N') { // use day for highs/night for lows if 'Temperatures nearly steady'
          $mtemp[1] = 'Highs';
          if (preg_match('|night|i',$day)) {
            $mtemp[1] = 'Lows';
          }
        }
        $tcolor = '#FF0000';
        if (strtoupper(substr($mtemp[1],0,1)) == 'L') {
          $tcolor = '#0000FF';
        }
        $temperature = ucfirst(substr($mtemp[1],0,2) . ' <span style="color: ' . $tcolor . '">');
        $t = $mtemp[3]; // the raw temp
        if (preg_match('/(low to mid|lower to mid|mid to upper|upper|lower|mid|in the)/',$mtemp[2],$ttemp) ) {
        //  $Status .= "<!-- ttemp " . print_r($ttemp,true) . " -->\n";
          $t = $t + 5;
          if ($ttemp[1] == 'upper') {
            $temperature .= '&gt;' . $t;
          }
          if ($ttemp[1] == 'lower') {
            $temperature .= '&lt;' . $t ;
          }
          if ($ttemp[1] == 'mid') {
            $temperature .= '&asymp;' . $t;
          }
          if ($ttemp[1] == 'in the') {
            $temperature .= '&asymp;' . $t;
          }
          if ($ttemp[1] == 'low to mid' or $ttemp[1] == 'lower to mid') {
            $t = $t -2;
            $temperature .= '&asymp;' . $t;
          }
          if ($ttemp[1] == 'mid to upper') {
            $t = $t + 2;
            $temperature .= '&asymp;' . $t;
          }
        }
        if (preg_match('/(near|around)/',$mtemp[2],$ttemp) ) {
          $temperature .= '&asymp;' . $mtemp[3];
        }
    $temperature .= '&deg;F</span>';
  }

  if (preg_match('/(Highs|Lows) ([\d|-]+) to ([\d|-]+)/i',$textforecast,$mtemp) ) { // temp range forecast
          $tcolor = '#FF0000';
        if (strtoupper(substr($mtemp[1],0,1)) == 'L') {
          $tcolor = '#0000FF';
        }
        $temperature = ucfirst(substr($mtemp[1],0,2) . ' <span style="color: ' . $tcolor . '">');

    $tavg = sprintf("%0d",round(($mtemp[3] + $mtemp[2]) / 2,0));
    $temperature .= '&asymp;' . $tavg . '&deg;F</span>';
  }

//  $Status .= "<!-- '$day'='$textforecast' -->\n";
   // now look for harshest conditions first.. (in order in -data file
 reset($Conditions);  // Do search in load order
 foreach ($Conditions as $cond => $condrec) { // look for matching condition

   if(preg_match("!$cond!i",$textforecast,$mtemp)) {
     list($dayicon,$nighticon,$condition) = explode("\t",$condrec);
         if (preg_match('|night|i',$day)) {
           $iconimage = $nighticon . $pop . '.jpg';
         } else {
           $iconimage = $dayicon . $pop . '.jpg';
         }
         break;
   }
 } // end of conditions search


  $icon .= '<img src="/forecast/images/' . $iconimage . '" height="58" width="55" ' .
     'alt="' . $condition . '" title="' . $condition . '" /><br/>' . $condition;

  return("$icon\t$temperature\t$pop");

} // end make_icons function
//------------------------------------------------------------------------------------------

// load the $Conditions array for icon selection based on key phrases
function load_cond_data () {
  global $Conditions, $Status;

$Condstring = '
#
cond|tornado|nsvrtsra|nsvrtsra|Severe storm|
cond|showery or intermittent. Some thunder|scttsra|nscttsra|Showers storms|
cond|thunder possible|scttsra|nscttsra|Showers storms|
cond|thunder|tsra|ntsra|Thunder storm|
cond|rain and sleet|raip|nraip|Rain Sleet|
cond|freezing rain and snow|raip|nraip|FrzgRn Snow|
cond|rain and snow|rasn|nrasn|Rain and Snow|
cond|rain or snow|rasn|nrasn|Rain or Snow|
cond|freezing rain|fzra|fzra|Freezing Rain|
cond|rain likely|ra|nra|Rain likely|
cond|showers likely|ra|nra|Showers likely|
cond|chance of rain|ra|nra|Chance rain|
cond|rain|ra|nra|Rain|
cond|mix|rasn|rasn|Mix|
cond|sleet|ip|ip|Sleet|
cond|snow|sn|nsn|Snow
cond|fog in the morning|sctfg|nbknfg|Fog a.m.|
cond|fog after midnight|sctfg|nbknfg|Fog late|
cond|fog|fg|nfg|Fog|
cond|wind chill down to -|cold|cold|Very Cold|
cond|heat index up to 1|hot|hot|Very Hot|
cond|overcast|ovc|novc|Overcast|
cond|mostly cloudy|bkn|nbkn|Mostly Cloudy|
cond|partly cloudy|sct|nsct|Partly Cloudy|
cond|cloudy|cloudy|ncloudy|Cloudy|
cond|partly sunny|sct|nsct|Partly Sunny|
cond|mostly sunny|few|nfew|Mostly Sunny|
cond|mostly clear|few|nfew|Mostly Clear|
cond|sunny|skc|nskc|Sunny|
cond|clear|skc|nskc|Clear|
cond|fair|few|nfew|Fair|
cond|cloud|bkn|nbkn|Variable Clouds|
#
';

$config = explode("\n",$Condstring);
foreach ($config as $key => $rec) { // load the parser condition strings
  $recin = trim($rec);
  if ($recin and substr($recin,0,1) <> '#') { // got a non comment record
    list($type,$keyword,$dayicon,$nighticon,$condition) = explode('|',$recin . '|||||');

        if (isset($type) and strtolower($type) == 'cond' and isset($condition)) {
          $Conditions["$keyword"] = "$dayicon\t$nighticon\t$condition";
//          $Status .= "<!-- '$keyword'='$dayicon','$nighticon' '$condition' -->\n";
        }
  } // end if not comment or blank
} // end loading of loop over config recs

} // end of load_cond_data function
//------------------------------------------------------------------------------------------

if (! $IncludeMode and $PrintMode) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>NWS Forecast for <?php echo $forecastcity; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; background-color:#FFFFFF">

<?php
}
print $Status;
// if the forecast text is blank, prompt the visitor to force an update

if (strlen($forecasttext[0])<2 and $PrintMode ) {

  echo '<br/><br/>Forecast blank? <a href="' . $PHP_SELF . '?force=1">Force Update</a><br/><br/>';

}
if ($PrintMode) {?>
  <table width="640" style="border: none;">
    <tr align="center" style="background-color: #FFFFFF;">
      <td><b>National Weather Service Forecast for: </b><span style="color: green;">
           <?php echo $forecastcity; ?></span><br />
        Issued by: <?php echo $forecastoffice; ?>
      </td>
    </tr>
    <tr>
      <td align="center">Updated: <?php echo $forecastupdated; ?>
          </td>
    </tr>
    <tr>
      <td align="center">&nbsp;
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr valign ="top" align="center">
        <?php
          for ($i=0;$i<count($forecasticons);$i++) {
            print "<td style=\"width: 11%;\"><span style=\"font-size: 8pt;\">$forecasticons[$i]</span></td>\n";
          }
        ?>
          </tr>
          <tr valign ="top" align="center">
          <?php
          for ($i=0;$i<count($forecasticons);$i++) {
            print "<td style=\"width: 11%;\">$forecasttemp[$i]</td>\n";
          }
          ?>
          </tr>
        </table>
     </td>
   </tr>
</table>
  <p>&nbsp;</p>

<table style="border: 0" width="640">
        <?php
          for ($i=0;$i<count($forecasttitles);$i++) {
        print "<tr valign =\"top\" align=\"left\">\n";
            print "<td style=\"width: 20%;\"><b>$forecasttitles[$i]</b><br />&nbsp;<br /></td>\n";
            print "<td style=\"width: 80%;\">$forecasttext[$i]</td>\n";
                print "</tr>\n";
          }
        ?>
   </table>

<p>&nbsp;</p>
<p>Forecast from <a href="<?php if($usingFile) {
 echo htmlspecialchars($backupfileName);
 } else {
  echo htmlspecialchars($fileName);
 } ?>">NOAA-NWS</a>
for <?php echo $forecastcity; ?>. <?php echo $usingFile; ?></p>
<?php
} // end printmode

 if (! $IncludeMode and $PrintMode ) { ?>
</body>
</html>
<?php } ?>
