<?php

require_once('system/Startup.class.php');

// Start the Cron job
$connector = new Connector();
$response = $connector->loadContent(CoreConfig::CRON_JOB_SERVICES);

var_dump($response);
?>
