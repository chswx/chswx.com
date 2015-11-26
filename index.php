<?php
include('config.php');

//
// Decode the data.
// 
$data = json_decode(file_get_contents(CHSWX_DATA_PATH),true);

//
// Set up data ahead of time.
//

// Current conditions
if(isset($data['current_observation'])) {
	$ob = $data['current_observation'];
	$temperature = $ob['temp_f'] . "&deg;";

	// Feels like (heat index/wind chill)
	$feels_like_temp = $ob['feelslike_f'] . "&deg;";
	if(!empty($ob['heat_index_f'])) {
		$feels_like_type = 'hi';
	}
	elseif(!empty($ob['windchill_f'])) {
		$feels_like_type = 'wc';
	}
	$display_feels_like = $feels_like_temp != $temperature;

	// Temperature color
	if($temperature < 28)
	{
		$tempcolor = "frigid";
	}
	else if ($temperature > 28 && $temperature < 50)
	{
		$tempcolor = "cold";
	}
	else if ($temperature >= 50 && $temperature < 70)
	{
		$tempcolor = "moderate";
	}
	else if ($temperature >= 70 && $temperature < 83)
	{
		$tempcolor = "warm";
	}
	else if ($temperature >= 83 && $temperature < 95)
	{
		$tempcolor = "verywarm";
	}
	else
	{
		$tempcolor = "hot";
	}

	// Sensible weather
	$sky = $ob['weather'];

	// Winds
	if($ob['wind_mph'] == 0) {
		$wind = "Calm";
	}
	else {
		$wind = $ob['wind_dir'] . " " . $ob['wind_mph'];
	}

	if($ob['wind_gust_mph'] > 0) {
		$wind .= " (gust {$ob['wind_gust_mph']})";
	}

	// Other statistics
	$dewpoint = $ob['dewpoint_f'] . "&deg;F";
	$rh = $ob['relative_humidity'];
	$pressure = $ob['pressure_in'] . " in";
}


// Set up the page's title.
$title = '';

if(isset($temperature) && isset($sky)) {
	$title = "Currently $temperature / $sky / ";
}

$title .= "#chswx - Charleston, SC Weather"

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title?></title>
<meta name=viewport content="width=device-width, initial-scale=1,maximum-scale=1.0, user-scalable=no">
<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png?v=A00YePnb9k">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png?v=A00YePnb9k">
<link rel="icon" type="image/png" href="/favicon-32x32.png?v=A00YePnb9k" sizes="32x32">
<link rel="icon" type="image/png" href="/android-chrome-192x192.png?v=A00YePnb9k" sizes="192x192">
<link rel="icon" type="image/png" href="/favicon-96x96.png?v=A00YePnb9k" sizes="96x96">
<link rel="icon" type="image/png" href="/favicon-16x16.png?v=A00YePnb9k" sizes="16x16">
<link rel="manifest" href="/manifest.json?v=A00YePnb9k">
<link rel="shortcut icon" href="/favicon.ico?v=A00YePnb9k">
<meta name="apple-mobile-web-app-title" content="#chswx">
<meta name="application-name" content="#chswx">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="msapplication-TileImage" content="/mstile-144x144.png?v=A00YePnb9k">
<meta name="theme-color" content="#ffffff">
<style type="text/css">
<?php include('inc/css/responsive-style.css'); ?>
</style>
<?php 
if(GOOGLE_ANALYTICS) {
	include('inc/ga.php');
}
?>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script>
$(document).ready(function($) {
	$('.alert').click(function(event) {
		var toggleID = '#' + event.currentTarget.id.toString();
		$(toggleID + ' ul').toggle();
	});
});
</script>
</head>
<body>

<h1 class="main-title"><span class="city" title="Charleston Weather">#chswx</span></h1>
<div id="wrapper">
<div id="currentwx">
<h2>Currently</h2>
<?php
if(isset($data['current_observation'])) { 
	?>
	<div id="temp" class="<?php echo $tempcolor?>"><?php echo $temperature?></div>
	<?php if ($display_feels_like): ?>
	<div id="feels-like">Feels Like <span class="<?php echo $feels_like_type?>"><?php echo $feels_like_temp?></span></div>
	<?php endif; ?>
	<div id="sky"><?php echo $sky;?></div>
	<ul id="others">
		<li><span class="title">Wind</span> <?php echo $wind?></li>
		<li><span class="title">Pressure</span> <?php echo $pressure;?></li>
		<li><span class="title">Dewpoint</span> <?php echo $dewpoint;?></li>
		<li><span class="title">Humidity</span> <?php echo $rh;?></li>
	</ul>
	<div class="updated-time">last updated <?php echo date('M j, Y g:ia',$ob['observation_epoch']); ?></div>
<?php } else { ?>
	<div class="fail">Temporarily Unavailable</div>
<?php } ?>
</div>
<?php if (!empty($data['alerts'])) 
{
?>
<div id="advisories">
	<h2>Alerts</h2>
	<ul>
	<?php foreach($data['alerts'] as $alert)
	{
		// try to filter out bad advisories
		$current_time = time();
		
		if($alert['phenomena'] == "TO")
		{
			$advisory_class = "tor";
		}
		else if($alert['phenomena'] == "SV")
		{
			$advisory_class = "svr";
		}
		else if($alert['phenomena'] == 'FL' || $alert['phenomena'] == 'FF')
		{
			$advisory_class = "ffw";
		}
		else
		{
			$advisory_class = "normal";
		}

		$alert_timing_text = '';

		if($alert['date_epoch'] > time()) {
			$alert_timing_text = "from {$alert['date']} ";
		}

		$alert_timing_text .= " until {$alert['expires']}";

		echo "<li class=\"alert vtec-phen-{$alert['phenomena']} vtec-sig-{$alert['significance']}\" id=\"{$alert['phenomena']}-{$alert['significance']}-{$alert['date_epoch']}\"><span class=\"alert-name\">" . $alert['description'] . "</span> <span class=\"alert-timing\">$alert_timing_text</span>";
		echo "<ul><li>" . str_replace("\n",'<br />',trim($alert['message'])) . "</li></ul></li>";
	}
	?>
</div>
<?php } ?>
<div id="forecast">
	<h2>Forecast</h2>
	<div class="updated-time"><a href="http://weather.gov/chs" target="_blank">NWS forecast</a> for Charleston updated at <?php echo $data['forecast']['txt_forecast']['date']?></div>
	<ul>
		<?php 
			if(isset($data['forecast']['txt_forecast']))
			{
				foreach($data['forecast']['txt_forecast']['forecastday'] as $forecast)
				{
					?><li><span class="day"><?php echo $forecast['title']?></span> <span class="forecast_text"><?php echo $forecast['fcttext']?></span></li>
					<?php
				}
			}
			else
			{
				?><div class="fail">Forecast temporarily unavailable</div>
			<?php }
		?>
	</ul>
</div>
</div>
<div id="footer"><h2>About @chswx</h2><div id="footer-wrapper">Follow Charleston Weather updates on <a href="http://twitter.com/chswx">Twitter</a> and <a href="http://facebook.com/chswx">Facebook</a> / Data by <a href="http://www.wunderground.com/US/SC/Charleston.html">Weather Underground</a><br /><br /><strong>Important!</strong> Use this page at your own risk. Not intended for use for life-or-death decisions. Refer to official statements from the National Weather Service/local emergency management in case of severe weather.</div>
</div>
</body>
<!-- Generated on <?php echo date('M j, Y g:ia'); ?> -->
</html>
