<?php

$path = dirname(__FILE__);
$cron = $path . "/run.php";
echo exec("***** php -q ".$cron." &> /dev/null");

?>