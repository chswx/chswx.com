#!/bin/bash
cd $CHSWX_PATH
wget --quiet -P ./data/ -N http://api.wunderground.com/api/$WU_API_KEY/alerts/conditions/forecast/q/KCHS.json > /dev/null
cp ./data/KCHS.json /home/chswx/wp/wp-content/uploads/
/usr/bin/php index.php > index.html
