<?php

/**
 * Created by Josua
 * Date: 26/05/2017
 * Time: 22:51
 */

require_once('system/Startup.class.php');

$tblStickiness = TblStickiness::getInstance();
$stickinessPending = $tblStickiness->getPending();

foreach($stickinessPending as $transaction)
{
  try
  {
    $customerId = $transaction['Customer_Id'];
    $personId = $transaction['Person_Id'];

    $stickiness = new Stickiness();
    $stickiness->setCustomerId($customerId);

    $person = new Person($personId);
    $stickiness->setPersonId($person->getPersonId());
    $stickiness->setPersonalId($person->getPersonalId());
    $stickiness->setPerson($person->getName());

    $stickiness->restore();
    $stickiness->verify();
  }
  catch(Exception $ex)
  {
    ExceptionManager::handleException($ex);
  }
}

Log::custom('Job', 'Services has finish');
?>