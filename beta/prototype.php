<?php include('inc/xml2array.php');

$observation = xml2array('test-data/kchs.xml');

//var_dump($observation);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Charleston, SC Weather: Sunny, 85 Degrees</title>
		<style type="text/css">
		body { background-image: url('images/background.jpg'); background-repeat: no-repeat; background-position: top center; background-color: rgb(76,113,163); margin: 20px 8%; font-family: 'Helvetica Neue', Arial, sans-serif; max-width: 1280px; }
		
		h1 { color: rgb(220,220,220); font-weight: 200; letter-spacing: -0.05em; padding: 0px; margin: 0px; float: left; font-size: 2.5em; text-shadow: 1px 1px 3px rgba(64,64,64,0.8); }
		
		h1 span.city { color: #FFF; font-weight: 600 !important; }
		
		div.clearance { clear:both; font-size: 0em; line-height: 0em; }
		
		#city-select
		{
			float: right;
			color: #fff;
			margin-top: 20px;
		}
		
		label[for="city-dropdown"]
		{
			font-size: 0.70em;
			text-transform: uppercase;
			color: #DDD;
		}
		
		
		#header { background-color: rgba(62,94,133,0.6); margin-top: -20px; padding: 20px 10px 10px }
		
        #advisories { margin-top: 15px; }
		
		#advisories .watch-tor { background-color: rgba(132,33,33,0.9); color: #FFF; }
		
		#advisories .watch-tstorm { background-color: rgba(213,196,55,0.9); color: #000; }
		
		#advisories ul { list-style: none; padding: 0px; margin: 0px; }
		
		#advisories ul li { padding: 7px; margin: 5px 0px; background-color: rgba(121,82,54,0.9); color: #FFF;}
		
		#advisories span.advisory { font-weight: 600 }
		
		#advisories span.tor, #advisories span.svr, #advisories span.ffw { text-transform: uppercase }
		
		#advisories ul li.warning-svr { background-color: rgba(217,1,16,0.9); }
		
		#advisories ul li.warning-tor { background-color: rgba(255,0,108,0.9); }
		
		#advisories ul li.warning-ffw { background-color: rgba(27,196,0,0.9); }
		
		#navigation { text-align: center; background-color: rgba(62,94,133,0.5); margin-top: 15px; padding: 10px; margin-bottom: 15px; line-height: 1.0em; }
		
		#navigation ul { list-style: none; padding: 0px; margin: auto; line-height: 1.0em; }
		
		#navigation ul li { display: inline; padding: 5px 25px; }
		
		#navigation ul li a { padding: 5px 15px; color: #FFF; text-decoration: none; text-transform: uppercase; line-height: 1.0em; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; /*text-shadow: rgba(32,32,32,0.8) 1px 1px 5px*/ }
		
		#navigation ul li a.current { color: rgb(62,94,133); background-color: rgba(255,255,255,0.9); text-shadow: none; font-weight: bold; }
		
		#navigation ul li a:hover { background-color: rgba(62,94,133,0.9); text-shadow: none; }
		
		#navigation ul li a.current:hover { background-color: rgba(62,94,133,1.0); color: #FFF !important;}
		
		#main-panel { background-color: rgba(255,255,255,0.97); padding: 0px 15px 15px; margin-top: 0px; /*-webkit-box-shadow: 0px 10px 20px rgba(64,64,64,0.5); -moz-box-shadow: 0px 10px 20px rgba(64,64,64,0.5);*/ }
		
		.current-conditions h2
		{
			margin: 0px 0px 0px -15px; padding: 6px; text-transform: uppercase; font-size: 0.80em; background-color: rgb(62,94,133); color: #FFF; font-weight: 200; text-align: left; margin-right: -15px;	
		}
		
		.current-conditions { float: left; width: 250px; border-right: 1px solid rgb(230,230,230); padding-right: 15px; }
		
		.current-conditions span.label { display: none }
		
		.current-conditions ul { list-style: none; padding: 0px; margin: 0px; }
		
		.current-conditions h2 span.city { font-weight: 500; }
		
		.current-conditions .temperature { text-align: center; display: block; font-size: 9.0em; font-weight: 600; letter-spacing: -0.05em; color: #FFF; background-color: rgb(253,121,3); margin-left: -15px; margin-right: -15px; }
		
		.current-conditions .sky { text-align: center; display: block; font-size: 2.5em; font-weight: 200; letter-spacing: -0.03em; line-height: 1.05em; margin-bottom: 20px; }
		
		.current-conditions ul li.heat-index { text-align: center; font-size: 1.8em; margin-bottom: 20px; font-weight: 200; background-color: rgb(200,0,0); color: #FFF; margin-left: -15px; margin-right: -15px; }
		
		.current-conditions ul li.wind-chill { text-align: center; font-size: 1.8em; margin-bottom: 20px; font-weight: 200; background-color: rgb(0,65,193); color: #FFF; margin-left: -15px; margin-right: -15px; }
		
		.current-conditions ul li span.feels-like-temperature { font-weight: 600 }
		
		.current-conditions ul li.addl-conditions { width: 115px; float: left; margin-right: 10px; margin-bottom: 15px; font-size: 0.70em; color: #666; text-transform: uppercase; }
		
		.current-conditions ul li.addl-conditions span { display: block; font-size: 2.5em; color: #000; font-weight: bold; letter-spacing: -0.06em; line-height: 1em; }
		
		.forecast-36 { margin-left: 280px; padding-top: 5px }
		
		.forecast-36 h2 { font-size: 0.80em; font-weight: 200; padding: 0px; margin-top: 0px; text-transform: uppercase; }
		
		.forecast-36 h2 .city, .forecast-36 .nowcast h2 { font-weight: 600; color: #000; margin: 0; padding: 0; color: rgb(62,94,133);}
		
		.forecast-36 .nowcast h2
		{
			font-size: 1.0em;
			color: #FFF; 
			background-color: rgb(64,64,64);
			padding: 5px;
			margin: 0px -5px;
		}
		
		.forecast-36 .nowcast h2 span.valid
		{
			font-size: 0.75em;
			font-weight: 200;
			display: block;
			float: right;
			padding-top: 2px;
			color: #DDD;
		}
		
		.nowcast
		{
			color: #000 !important;
			background-color: rgb(252,238,181);
			padding: 0px 5px;
			border: 1px solid rgb(252,230,140);
			font-size: 0.80em;
			margin-bottom: 15px;
		}
		
		.nowcast p
		{
			line-height: 1.5em;
		}
		 
		</style>
	</head>
	<body>
		<div id="header">
		
			<h1><span class="city">Charleston</span> Weather</h1>
			
			<form id="city-select">
				<label for="city-dropdown">Select Your Area</label>
				<select accesskey="c">
					<option selected="selected">Downtown</option>
					<option>West Ashley</option>
					<option>Mt. Pleasant/East Cooper</option>
					<option>James Island/Folly Beach</option>
					<option>Summerville</option>
					<option>North Charleston/Goose Creek</option>
					<option>Moncks Corner</option>
				</select>
			</form>
		<div class="clearance"><!-- intentionally blank --></div>
		</div>
		<div id="advisories">
			 <ul>
				<li class="warning-tor"><span class="advisory tor">Tornado Warning</span> for Berkeley, Charleston until 10:00 PM</li>
				<li class="warning-svr"><span class="advisory svr">Severe Thunderstorm Warning</span> for Berkeley, Charleston until 10:00 PM</li>
			<!--	<li class="warning-ffw"><span class="advisory ffw">Flash Flood Warning</span> for Berkeley, Charleston until 10:00 PM</li> 
				<li class="watch-tstorm"><span class="advisory swatch">Severe Thunderstorm Watch</span> for southeast South Carolina until 11:00 PM</li>
				<li class="watch-tor"><span class="advisory twatch">Tornado Watch</span> for southeast South Carolina until 11:00 PM</li>
				<li class="advisory-sws"><span class="advisory sws">Special Weather Statement</span> for Berkeley, Charleston until 10:00 PM</li> -->
			</ul> 
		</div>
		<!--<div id="navigation">
			<ul>
				<li><a href="#" class="current">Now</a></li>
				<li><a href="#">Radar</a></li>
				<li><a href="#">Extended Forecast</a></li>
				<li><a href="#">Marine</a></li>
			</ul>
			<div class="clearance"></div>
		</div>-->
		<div id="main-panel">
			<div class="current-conditions">
				<h2>Currently at <span class="city">Charleston</span></h2>
				<ul>
					<li><span class="label">Temperature: </span><span class="temperature"><?php echo round($observation['current_observation']['temp_f'])?></span></li>
					<?php if($observation['windchill_f'] || $observation['heatindex_f']) { ?><li class="wind-chill">Feels Like <span class="feels-like-temperature">92</span></li> <?php } ?>
					<li><span class="label">Sky: </span><span class="sky"><?php echo $observation['current_observation']['weather']?></span></li>
					<li class="addl-conditions">Humidity <span class="humidity"><?php echo $observation['current_observation']['relative_humidity']?>%</span></li>
					<li class="addl-conditions">Dewpoint <span class="dewpoint"><?php echo round($observation['current_observation']['dewpoint_f'])?></span></li>
					<li class="addl-conditions">Pressure <span class="pressure"><?php echo $observation['current_observation']['pressure_in']?> in.</span></li>
					<li class="addl-conditions">Wind <span class="wind"><?php echo $observation['current_observation']['wind_dir']?> <?php echo round($observation['current_observation']['wind_mph'])?></span></li>
					<li class="addl-conditions">Visibility <span class="visibility"><?php echo $observation['current_observation']['visibility_mi']?> mi.</span></li>
				</ul>
			</div>
			<div class="forecast-36">
				<div class="nowcast">
					<h2>Short Term Forecast <span class="valid">Valid until 12 AM</span></h2>
					<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Etiam sit amet elit vitae arcu interdum ullamcorper. Nullam ultrices, nisi quis scelerisque convallis, augue neque tempor enim, et mattis justo nibh eu elit. Quisque ultrices gravida pede. Mauris accumsan vulputate tellus. Phasellus condimentum bibendum dolor. Mauris sed ipsum. Phasellus in diam. Nam sapien ligula, consectetuer id, hendrerit in, cursus sed, leo. Nam tincidunt rhoncus urna. Aliquam id massa ut nibh bibendum imperdiet. Curabitur neque mauris, porta vel, lacinia quis, placerat ultrices, orci.</p>
				</div>
				<h2>36 Hour Forecast for <span class="city">Charleston</span></h2>
				<ul>
		<li><span class="day">
	Rest of Tonight</span> <span class="hidden">| </span><span class="forecast_text">Mostly clear. Lows around 50...except in the mid 50s near the coast. Northeast winds 5 to 10 mph.
      </span></li>
					<li><span class="day">
	Monday</span> <span class="hidden">| </span><span class="forecast_text">Sunny. Highs in the lower 70s. Northeast winds 5 to 10 mph. 
      </span></li>

					<li><span class="day">
	Monday Night</span> <span class="hidden">| </span><span class="forecast_text">Mostly clear in the evening...then becoming partly cloudy. Lows in the upper 40s...except in the lower 50s near the coast. Southeast winds 5 to 10 mph. 
      </span></li>
						</ul>
			</div>
			<div class="clearance"><!-- intentionally blank --></div>
		</div>
		<!-- <ul>
			<li>Current Conditions
				<ul id="current-conditions">
					
			</li>
			<li>Extended Forecast
				<ul id="extended-forecast">
					<li><span class="label day">Tonight</span><span class="hide">:</span> <span class="forecast-text">Mostly cloudy.  Low 56.  Wind WSW at 10 to 20 MPH.</span></li>
					<li><span class="label day">Tuesday</span><span class="hide">:</span> <span class="forecast-text">Partly cloudy.  High 81.  Wind WSW at 5 to 10 MPH.</span></li>
				</ul>
			</li>
		</ul> -->
	</body>
</html>
