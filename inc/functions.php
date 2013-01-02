<?php

include('magpie/rss_fetch.inc');

$wx_data = "http://rss.wunderground.com/auto/rss_full/SC/KCHS.xml";
$rss = fetch_rss($wx_data);
function getWxData($feed)
{
	
	$maxitems = 1;

	if(!empty($feed)) {
	$items = array_slice($feed->items, 0, $maxitems);
	$wx_pre_parse = $items[0]['description'];

	$raw_wx = explode(' | ',$wx_pre_parse);

	// Update time
	$meta = array_slice($feed->channel,0);
	$update_time = /*strtotime(*/$meta['pubdate']/*)*/;
	$wx['update_time'] = $update_time;
	
	//
	// Get the temperature cleaned up
	//
	
	$temp_string = substr($raw_wx[0], 13);
	$temp_array = explode(' / ',$temp_string);
	
	$wx['temperature']['f'] = $temp_array[0];
	$wx['temperature']['c'] = $temp_array[1];
	
	// Add humidity to array
	
	$hum_string = substr($raw_wx[1], 10);
	$wx['humidity'] = $hum_string;
	
	// Add pressure to array
	
	$bar_string = substr($raw_wx[2], 10);
	$bar_array = explode(' / ',$bar_string);
	
	$wx['pressure']['in'] = $bar_array[0];
	$wx['pressure']['hpa'] = $bar_array[1];
	
	// Add sky conditions
	
	$sky_string = substr($raw_wx[3],11);
	$wx['sky'] = $sky_string;
	
	// Add wind dir
	
	$wind_dir_string = substr($raw_wx[4],16);
	$wx['wind_dir'] = $wind_dir_string;
	
	// Add windspeed
	
	$wind_speed_string = substr($raw_wx[5],12);
	$wind_speed_array = explode(' / ',$wind_speed_string);
	
	$wx['wind_speed']['mph'] = $wind_speed_array[0];
	$wx['wind_speed']['kph'] = $wind_speed_array[1];
	}
	else
	{
		$wx = 0;
	}
	return $wx;
}

function getForecast($feed)
{
	if(!empty($feed))
	{
		$items = array_slice($feed->items, 1, 3);	// start with the second item, get three in total
		
		for($i = 0; $i < 3; $i++)
		{
			$rawdata = $items[$i]['description'];
			$exploded = explode(' - ', $rawdata);
			$forecast_data[$i]['day'] = $exploded[0];
			$forecast_data[$i]['desc'] = $exploded[1];
		}
		
		
	
	}
	else
	{
		$forecast_data = null;
	}
	
	return $forecast_data;
}

function getAdvisories()
{
	$atom_feed_url = "http://www.weather.gov/alerts-beta/sc.php?x=0";
	$wx_alerts = fetch_rss('http://www.weather.gov/alerts-beta/sc.php?x=0');
	
	return $wx_alerts;
}

function getAdvisoriesWU($feed)
{
	$advisory = null;
	
	if(!empty($feed))
	{
		$items = array_slice($feed->items, 4, count($feed->items));	 // start with the fourth item, get the rest	
		for($i = 0; $i < count($items); $i++)
		{
			$rawdata = $items[$i]['title'];
			$exploded = explode(' - Expires: ',$rawdata);
			$advisory[$i]['type'] = $exploded[0];
			$advisory[$i]['expires'] = $exploded[1];
		}
	}
	
	return $advisory;
}
?>