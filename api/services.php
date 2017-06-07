<?php

/**
 * Created by Josua
 * Date: 26/05/2017
 * Time: 22:51
 */

require_once('system/Startup.class.php');

Log::custom('Job', 'Service has started');

$tblStickiness = TblStickiness::getInstance();

$stickinessPending = $tblStickiness->getPending();
foreach($stickinessPending as $pending)
{
  try
  {
    $customerId = $pending['Customer_Id'];
    $personId = $pending['Person_Id'];

    $stickiness = new Stickiness();
    $stickiness->setCustomerId($customerId);

    $person = new Person($personId);
    $stickiness->setPersonId($person->getPersonId());
    $stickiness->setPersonalId($person->getPersonalId());
    $stickiness->setPerson($person->getName());

    $stickiness->restore();
    $stickiness->verify();

    $logData = Util::toString($pending);
    Log::custom('Job', 'Check Pending Transaction', $logData);
  }
  catch(Exception $ex)
  {
    ExceptionManager::handleException($ex);
  }
}

$stickinessApproved = $tblStickiness->getApproved();
foreach($stickinessApproved as $approved)
{
  try
  {
    $customerId = $approved['Customer_Id'];
    $personId = $approved['Person_Id'];

    $stickiness = new Stickiness();
    $stickiness->setCustomerId($customerId);

    $person = new Person($personId);
    $stickiness->setPersonId($person->getPersonId());
    $stickiness->setPersonalId($person->getPersonalId());
    $stickiness->setPerson($person->getName());

    $stickiness->restore();
    $stickiness->verify();

    $logData = Util::toString($approved);
    Log::custom('Job', 'Check Approved Transaction', $logData);
  }
  catch(Exception $ex)
  {
    ExceptionManager::handleException($ex);
  }
}

Log::custom('Job', 'Service has finish');
?>