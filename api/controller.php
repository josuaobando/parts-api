<?php

require_once('system/Startup.class.php');

/**
 * here is where we start processing the request
 * checking what function is being requested
 */
function startController()
{
  //check if the request was sent using json
  $wsRequest = new WSRequest(json_decode(file_get_contents("php://input"), true));
  if($wsRequest->isEmpty()){
    //if empty try with the regular request
    $wsRequest->overwriteRequest($_REQUEST);
  }

  //prefix in order to avoid a conflict with the function names in the webservices
  $prefix = "ctrl_";

  //check if the requested function is valid
  $f = $wsRequest->getParam(WSProcessor::REQUESTED_FUNCTION);

  //get session id
  $sessionId = $wsRequest->getParam('sid');
  if($sessionId){
    Session::startSession($sessionId);
    $account = Session::getAccount();
    if($account->isAuthenticated()){
      //call the proper function
      if(function_exists($prefix.$f)){
        //call the function and exit since the function will do the whole work
        call_user_func($prefix.$f);
        exit();
      }else{
        //this section is to handle the invalid function error
        $wsResponse = new WSResponseError("Invalid function in controller($f)");
      }
    }else{
      //this section is to handle the invalid function error
      $wsResponse = new WSResponseError('Session has expired');
    }
  }

  //set the header of the response
  Util::putResponseHeaders(WSResponse::FORMAT_JSON, 'UTF-8');

  //send the object to the output converting it to string
  echo $wsResponse->toString(WSResponse::FORMAT_JSON);
}

/**
 * login account
 */
function ctrl_login()
{
  require_once('api/client.php');
}

/**
 * get countries
 */
function ctrl_getCountries()
{
  require_once('api/client.php');
}

/**
 * get agencies
 */
function ctrl_getAgencies()
{
  require_once('api/client.php');
}

/**
 * start controller
 */
startController();

?>