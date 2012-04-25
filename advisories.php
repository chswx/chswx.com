<?php
include('inc/functions.php');
$alerts = get_advisories();
$item = $alerts->items[0];
echo $item;
?>