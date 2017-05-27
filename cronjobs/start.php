<?php

require_once('system/Startup.class.php');

if(CoreConfig::CRON_JOBS_ACTIVE)
{
  // Start the Cron job
  $connector = new Connector();
  $response = $connector->loadContent(CoreConfig::CRON_JOB_SERVICES);

  var_dump($response);
}
else
{
  echo 'Services are turned off!';
}

?>
