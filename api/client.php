<?php

/**
 * Gustavo Granados
 * code is poetry
 */

require_once('system/Startup.class.php');

/**
 * account login
 *
 * @param WSRequest $wsRequest
 *
 * @return WSResponse
 */
function login($wsRequest)
{
  try
  {
    $username = trim($wsRequest->requireNotNullOrEmpty('username'));
    $password = trim($wsRequest->requireNotNullOrEmpty('password'));

    $account = new Account($username);
    $account->authenticate($password);

    $wsResponse = new WSResponseOk();
    $wsResponse->addElement('account', $account);
  }
  catch(InvalidParameterException $ex)
  {
    $wsResponse = new WSResponseError($ex->getMessage());
  }

  return $wsResponse;
}

/**
 * get agencies
 *
 * @param WSRequest $wsRequest
 *
 * @return WSResponse
 */
function getAgencies($wsRequest)
{
  try
  {
    $agencies = Session::getCountries();
    $wsResponse = new WSResponseOk();
    $wsResponse->addElement('agencies', $agencies);
  }
  catch(InvalidParameterException $ex)
  {
    $wsResponse = new WSResponseError($ex->getMessage());
  }

  return $wsResponse;
}

WSProcessor::process();

?>