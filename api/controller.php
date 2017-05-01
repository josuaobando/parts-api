<?php

/**
 * Gustavo Granados
 * code is poetry
 */

require_once ('system/Startup.class.php');

/**
 * here is where we start processing the request
 * checking what function is being requested
 */
function startController()
{
	//create the request object
	$wsRequest = new WSRequest($_REQUEST);

  //prefix in order to avoid a conflict with the function names in the webservices
  $prefix = "ctrl_";

  //check if the requested function is valid
  $f = $wsRequest->getParam(WSProcessor::REQUESTED_FUNCTION);

  //get session id
  $sessionId = $wsRequest->getParam('sid');
  if($sessionId)
  {
    session_id($sessionId);
    session_start();

    $account = Session::getAccount();
    if($account->isAuthenticated())
    {
      //call the proper function
      if (function_exists($prefix.$f))
      {
        //call the function and exit since the function will do the whole work
        call_user_func($prefix.$f);
        exit();
      }
    }
  }
	
	//this section is to handle the invalid function error
	$wsResponse = new WSResponseError("Invalid function in controller($f)");
	$format = $wsRequest->getParam(WSProcessor::REQUESTED_FORMAT, WSResponse::FORMAT_JSON);
	$encoding = $wsRequest->getParam('encoding', 'UTF-8');
			
	//set the header of the response
	Util::putResponseHeaders($format, $encoding);
	
	//send the object to the output converting it to string
	echo $wsResponse->toString($format);
}

/**
 * login account
 */
function ctrl_login()
{
  require_once ('api/client.php');
}


/**
 * get agencies
 */
function ctrl_getAgencies()
{
  require_once ('api/client.php');
}

/**
 * start controller
 */
startController();

?>