#!/bin/bash
cd $CHSWX_PATH
wget --quiet -P ./data/ -N http://api.wunderground.com/api/$WU_API_KEY/alerts/conditions/forecast/bestfct:0/q/KCHS.json > /dev/null
/usr/bin/php index.php > index.html
