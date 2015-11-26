#!/bin/bash
cd $CHSWX_PATH
wget --quiet -P /home/chswx/www/data/ -N http://api.wunderground.com/api/$WU_API_KEY/alerts/conditions/forecast/bestfct:0/q/KCHS.json > /dev/null
/usr/bin/php /home/chswx/www/index.php > /home/chswx/www/index.html
