<?php
include('config.php');

$data = json_decode(file_get_contents(CHSWX_DATA_PATH),true);
$temperature = $data['current_observation']['temp_f'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Currently <?php if(isset($data)) { echo $temperature ?> / <?php echo $data['current_observation']['weather']; } ?> | Charleston, SC Weather</title>
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
<?php include('inc/css/style.css'); ?>
</style>
<?php if(GOOGLE_ANALYTICS): ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-438397-7']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php endif; ?>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script>
$(document).ready(function($) {
	$('.alert-title').click(function(event) {
		console.log(event.target.parentNode.id.toString());
		var parentID = '#' + event.target.parentNode.id.toString();
		$(parentID + ' ul').toggle();
	});
});
</script>
</head>
<body>
<div id="wrapper">
<h1><span class="city" title="Charleston Weather">#chswx</span></h1>
<div id="current-and-forecast-wrapper">
<h2>Currently</h2>
<div id="currentwx">
<?php
// determine temperature color

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
if(isset($data['current_observation'])) { 
	$ob = $data['current_observation'];

	?>
	<div id="temp" class="<?php echo $tempcolor?>"><?php echo $temperature?></div>
	<div id="sky"><?php echo $ob['weather'];?></div>
	<div id="others">
		<?php if ($ob['feelslike_f'] != $temperature): ?><span class="title">Feels like</span> <?php echo $ob['feelslike_f']?><?php endif; ?>
		<span class="title">Dewpoint</span> <?php echo $ob['dewpoint_f'];?> 
		<span class="title">Humidity</span> <?php echo $ob['relative_humidity'];?> 
		<span class="title">Pressure</span> <?php echo $ob['pressure_in'];?> 
		<span class="title">Wind</span> <?php echo $ob['wind_dir'];?>&nbsp;<?php echo $ob['wind_mph']?> <?php if($ob['wind_gust_mph'] > 0): ?>(gusts to <?php echo $ob['wind_gust_mph']?>)<?php endif ;?>
	</div>
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
		echo "<li class=\"alert\" id=\"{$alert['phenomena']}-{$alert['significance']}-{$alert['date_epoch']}\"><span class=\"alert-title " . $advisory_class . "\"><span class=\"alert-name\">" . $alert['description'] . "</span> until " . $alert['expires'] . ".</span>";
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
</div>
<div id="footer"><div id="footer_wrapper">Follow Charleston Weather updates on <a href="http://twitter.com/chswx">Twitter</a> and <a href="http://facebook.com/chswx">Facebook</a> / Data by <a href="http://www.wunderground.com/US/SC/Charleston.html">Weather Underground</a><br /><br /><strong>Disclaimer:</strong> Use this page at your own risk. Not intended for use for life-or-death decisions. Refer to official statements from the National Weather Service/local emergency management in case of severe weather.</div>
</div>
</body>
<!-- Generated on <?php echo date('M j, Y g:ia'); ?> -->
</html>
