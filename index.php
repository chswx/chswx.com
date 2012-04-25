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
<title>Currently <?php if($conditions != 0) { echo $temperature ?> / <?php echo $conditions['sky']; } ?></title>
<link rel="icon" href="images/favicon.png" type="image/png" />
<style type="text/css">
html
{
	background-image: url('background.jpg');
	background-repeat: no-repeat;
	background-position: top center;
	background-color: #4C71A3;
}

body
{
	font-family: 'Helvetica Neue',Arial,Helvetica,sans-serif;
	padding: 0;
	margin: 0;
	text-align: center;
}

#wrapper {
width: 800px;
	margin: auto;
	}

#current-and-forecast-wrapper
{
	background-color: rgba(255,255,255,0.7);
	border-radius: 10px;
	-moz-border-radius: 10px;
	padding: 10px;
	margin-bottom: 20px;
}

.hidden
{
	display: none;
}

h1 { color: rgb(220,220,220); font-weight: 200; letter-spacing: -0.05em; padding: 10px 0px; margin: 0 0 20px 0; font-size: 2.5em; text-shadow: 1px 1px 3px rgba(64,64,64,0.8); background-color: rgba(62,94,133,0.6); -webkit-border-bottom-right-radius: 10px;
-webkit-border-bottom-left-radius: 10px;
-moz-border-radius-bottomright: 10px;
-moz-border-radius-bottomleft: 10px;
border-bottom-right-radius: 10px;
border-bottom-left-radius: 10px; }

h1 span.city { color: #FFF; font-weight: 600 !important; }

h2
{
	text-transform: uppercase;
	font-size: 1.45em;
	/*color: #FFF;*/
	font-weight: normal;
	text-align: center;
	letter-spacing: -0.04em;
	color: rgb(62,94,133);
	font-weight: 200;
	-webkit-border-radius: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
    margin: 10px 0px;
}

div#currentwx
{
	text-align: center;
	padding: 20px;
}

div#currentwx #temp
{
	font-size: 10.5em;
	font-weight: bold;
	letter-spacing: -0.05em;
	line-height: 0.9em;
}

div#currentwx #temp.frigid
{
	color: rgb(131,62,153);
}

div#currentwx #temp.cold
{
	color: rgb(27,54,155);
}

div#currentwx #temp.moderate
{
	color: rgb(0,132,68);
}

div#currentwx #temp.warm
{
	color: rgb(210,178,0);
}

div#currentwx #temp.verywarm
{
	color: rgb(210,104,0);
}

div#currentwx #temp.hot
{
	color: rgb(146,0,0);
}

div#currentwx #sky
{
	font-size: 2.45em;
	color: rgb(64,64,64);
	letter-spacing: -0.05em;
	font-weight: bold;
	text-transform: lowercase;
}

div#currentwx #others
{
	font-weight: bold;
	padding-top: 10px;
}

div#currentwx #others span
{
	font-weight: normal;
	text-transform: lowercase;
	color: rgb(128,128,128);
}

div#advisories ul
{
	list-style-type:none;
	font-weight: bold;
	font-size: 1.15em;
	margin: 0px;
	padding: 0px;
}

div#advisories .tor
{
	background-color: rgb(146,0,0);
	color: rgb(255,255,255);
}

div#advisories .svr
{
	background-color: rgb(210,178,0);
	color: rgb(255,255,255);
}

div#forecast
{
	width: 760px;
	margin: auto;
	text-align: left !important;
	padding-bottom: 10px;
}

div#forecast ul
{
	list-style-type: none;
	margin: 0px;
	padding: 0px;
	padding-left: 20px;
}

div#forecast ul li
{
	padding-bottom: 20px;
	font-size: 1.3em;
}

div#forecast ul li span.day
{
	font-weight: bold;
	letter-spacing: -0.05em;
	color: rgb(128,128,128);
}

div#footer
{
	/*width: 100%;*/
	text-align: center;
	color: rgb(200,200,200);
	font-size: 0.70em;
	padding: 5px;
	background-color: rgb(64,64,64);
}

div#footer-wrapper
{
	width: 760px;
	margin: auto;
}

div#footer a
{
	color: rgb(224,224,224);
	text-decoration: none;
	font-weight: bold;
}

div#footer a:hover
{
	color: rgb(245,245,245);
}
</style>
<meta http-equiv="refresh" content="2700;url=http://charlestonwx.com" />
</head>
<body>
<div id="wrapper">
<h1><span class="city">Charleston</span> Weather</h1>
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
		// try to filter out bad advisories
		$current_time = time();
		
		if($advisories[$i]['type'] == "Tornado Watch")
		{
			$advisory_class = "tor";
		}
		else if($advisories[$i]['type'] == "Severe Thunderstorm Watch")
		{
			$advisory_class = "svr";
		}
		else if($advisories[$i]['type'] == "Flash Flood Watch" || $advisories[$i]['type'] == "Flash Flood Warning")
		{
			$advisory_class = "ffw";
		}
		else
		{
			$advisory_class = "normal";
		}
		if($advisory_class == "tor" || $advisory_class == "svr" || $advisory_class == "ffw") {
			echo "<li><span class=\"" . $advisory_class . "\">" . $advisories[$i]['type'] . "</span> until " . $advisories[$i]['expires'] . ".</li>";
		}
	}
	?>
</div>
<?php } ?>
<div id="forecast">
	<h2>Forecast</h2>
	<ul>
		<?php 
			if(sizeof($forecast) == 3)
			{
				for($i = 0; $i < sizeof($forecast); $i++)
				{
					?><li><span class="day"><?php echo $forecast[$i]['day']?></span> <span class="hidden">| </span><span class="forecast_text"><?php echo $forecast[$i]['desc']?></span></li>
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
<div id="footer"><div id="footer_wrapper">Follow Charleston Weather updates on <a href="http://twitter.com/chswx">Twitter</a> and <a href="http://identi.ca/chswx">identi.ca</a> / Data by <a href="http://www.wunderground.com/US/SC/Charleston.html">Weather Underground</a><br /><br /><strong>Disclaimer:</strong> Use this page at your own risk. Not intended for use for life-or-death decisions. Refer to official statements from the National Weather Service/local emergency management in case of severe weather.</div>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-438397-7']);
  _gaq.push(['_setDomainName', '<?php echo $_SERVER['HTTP_HOST']?>');
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
<!-- Served by <?php echo $_SERVER['HTTP_HOST']; ?> -->
</html>
