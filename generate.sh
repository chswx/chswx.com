#!/bin/bash
wget --quiet -P /home/chswx/www/data/ -N http://api.wunderground.com/api/0e777ccb1a96f568/alerts/conditions/forecast/bestfct:0/q/KCHS.json > /dev/null;
/usr/bin/php /home/chswx/www/index.php > /home/chswx/www/index.html
