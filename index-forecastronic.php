<?php

// chswx test!  bring it on apw!

include('inc/functions.php');
$conditions = getWxData($rss);
$forecast = getForecast($rss);
$advisories = getAdvisoriesWU($rss);
$sanitized_temp_array = explode('.',$conditions['temperature']['f']);
$temperature = $sanitized_temp_array[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Charleston Weather <?php if($conditions != 0) { ?>/ <?php echo $temperature ?> / <?php echo $conditions['sky']; } ?></title>
<link rel="icon" href="images/favicon.png" type="image/png" />
<link rel="stylesheet" type="text/css" href="styles.css" />
<meta http-equiv="refresh" content="2700;url=http://charlestonwx.com" />
</head>
<body>
<h1>Charleston Weather</h1>
<h2 class="hidden">Current Conditions</h2>
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
if($conditions != 0) { ?>
	<div id="temp" class="<?php echo $tempcolor?>"><?php echo $temperature?></div>
	<div id="sky"><?php echo $conditions['sky'];?></div>
	<div id="others"><span class="title">Humidity</span> <?php echo $conditions['humidity'];?> 
		<span class="title">Pressure</span> <?php echo $conditions['pressure']['in'];?> 
		<span class="title">Wind</span> <?php echo $conditions['wind_dir'];?>&nbsp;<?php echo $conditions['wind_speed']['mph']?>
	</div>
<?php } else { ?>
	<div class="fail">Temporarily Unavailable</div>
<?php } ?>
</div>
<?php if (!empty($advisories)) 
{
?>
<div id="advisories">
	<ul>
	<?php for($i = 0; $i < sizeof($advisories); $i++)
	{
		if($advisories[$i]['type'] == "Tornado Watch")
		{
			$advisory_class = "tor";
		}
		else if($advisories[$i]['type'] == "Severe Thunderstorm Watch")
		{
			$advisory_class = "svr";
		}
		else
		{
			$advisory_class = "normal";
		}
		echo "<li><span class=\"" . $advisory_class . "\">" . $advisories[$i]['type'] . "</span> until " . $advisories[$i]['expires'] . ".</li>";
	}
	?>
</div>
<?php } ?>
<div id="forecast">
	<h2>Your Hypercertified Forecast</h2>
	<img src="images/hypercertified.png" alt="Forecastronic 9000 Hypercertified Forecast" style="float: right" />
	<ul>
		<li><span class="day">Today</span> <span class="hidden">| </span> <span class="forecast_text">A graphical representation of the sun obscured by a cloud that appears to be emitting rain.</span></li>
		<li><span class="day">Tonight</span> <span class="hidden">| </span> <span class="forecast_text">Night.</span></li>
		<li><span class="day">Thursday</span> <span class="hidden">| </span> <span class="forecast_text">Lapse rates.</span></li>
		<?php 
		/*	if(sizeof($forecast) == 3)
			{
				for($i = 0; $i < sizeof($forecast); $i++)
				{
					?><li><span class="day"><?php echo $forecast[$i]['day']?></span> <span class="hidden">| </span><span class="forecast_text"><?php echo $forecast[$i]['desc']?></span></li>
					<?php
				}
			}
			else
			{ */
				?><div class="fail"><a href="http://blog.charlestonwx.com/2009/04/01/charlestonwxcom-brings-forecasts-in-house-with-new-computer-model/">About our new forecast model</a></div>
			<?php/* } */
		?>
	</ul>
</div>
<div id="footer">serving charleston, sc / follow <a href="http://twitter.com/chswx">@chswx</a> on <a href="http://twitter.com/chswx">twitter</a> and <a href="http://identi.ca/chswx">identi.ca</a> / forecast by <a href="http://twitter.com/forecastronic">forecastronic</a> / <a href="http://blog.charlestonwx.com">weather blog</a> / another <a href="http://somnambulonimbus.com">subconsciously stratospheric</a> idea</div>
</body>
</html>