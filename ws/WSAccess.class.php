<?php

/**
 * @author Josua
 */
class WSAccess
{

  /**
   * parameter identifier for the access password of the service
   *
   * @var string
   */
  const SYS_ACCESS_PASS = 'apiKey';

  /**
   * it retrieves the request ip address
   *
   * @return string
   */
  private static function getRequestIP()
  {
    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
      $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    }else{
      $ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] . "," : "") . $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }

  /**
   * it checks if the requested service is valid and is active.
   * it returns the webservice data
   *
   * @param string $webservice
   *
   * @throws AccessDeniedException
   *
   * @return array
   */
  private static function checkWebservice($webservice)
  {
    $tblWSAccess = TblWSAccess::getInstance();
    $webserviceData = $tblWSAccess->getWebservice($webservice);
    if(!$webserviceData){
      throw new AccessDeniedException("service $webservice not found", "Check webservice '$webservice' has failed");
    }
    if(!$webserviceData['Active']){
      throw new AccessDeniedException("service not available", "The webservice '$webservice' is currently inactive");
    }

    return $webserviceData;
  }

  /**
   * it checks if the access password sent is valid and is active.
   * it returns the webservice user data
   *
   * @param string $apiKey
   *
   * @throws AccessDeniedException
   *
   * @return array
   */
  private static function checkWebserviceUser($apiKey)
  {
    if(!$apiKey){
      throw new AccessDeniedException("missing authentication key", "The access password is required");
    }

    $tblWSAccess = TblWSAccess::getInstance();
    $webserviceUserData = $tblWSAccess->getWebserviceUser($apiKey);
    if(!$webserviceUserData){
      throw new AccessDeniedException("invalid authentication key", "The access password is invalid: '$apiKey'");
    }
    if(!$webserviceUserData['Active']){
      throw new AccessDeniedException("authentication key not available", "The access password '$apiKey' is currently inactive");
    }

    return $webserviceUserData;
  }

  /**
   * it checks if the webservice access.
   * it returns the webservice access data
   *
   * @param int $webserviceId
   * @param int $webserviceUserId
   *
   * @throws AccessDeniedException
   *
   * @return array
   */
  private static function checkWebserviceAccess($webserviceId, $webserviceUserId)
  {
    $tblWSAccess = TblWSAccess::getInstance();
    $webserviceAccessData = $tblWSAccess->getWebserviceAccess($webserviceId, $webserviceUserId);
    if(!$webserviceAccessData){
      throw new AccessDeniedException("service restricted", "Not access granted to this webservice ($webserviceId, $webserviceUserId)");
    }
    if(!$webserviceAccessData['Active']){
      throw new AccessDeniedException("access not available", "The access to this service ($webserviceId, $webserviceUserId) is currently inactive");
    }

    return $webserviceAccessData;
  }

  /**
   * check the requestor IP address
   *
   * @param $ip
   * @param $webserviceUserData
   *
   * @return mixed
   *
   * @throws AccessDeniedException
   */
  private static function checkIPs($ip, $webserviceUserData)
  {
    $ips = $webserviceUserData['IPs'];
    $accessPassword = $webserviceUserData['AccessPassword'];
    if(!$ips){
      throw new AccessDeniedException("service restricted by IP address", "This access password ($accessPassword) does not have allowed ips assigned");
    }
    if(!$ip){
      throw new AccessDeniedException("invalid IP address", "There was an issue detecting the IP address");
    }

    $requestIPs = explode(",", $ip);
    $allowedIPs = explode("\n", $ips);
    $found = array_intersect($requestIPs, $allowedIPs);
    if(!in_array("*", $allowedIPs) && (!$found || count($found) == 0)){
      throw new AccessDeniedException("this IP address has not access to this service. $ip", "The IP address ($ip) is not allowed");
    }

    return array_shift($found);
  }

  /**
   * get the credentials of the request
   *
   * @param WSRequest $wsRequest
   *
   * @return array
   */
  public static function getCredentials($wsRequest)
  {
    //get the service being requested
    $webservice = basename($_SERVER['SCRIPT_FILENAME']);      //get the file name
    $webservice = strpos($webservice, ".") ? substr($webservice, 0, strpos($webservice, ".")) : $webservice; //remove the extension

    //we get the access password sent with the request
    $apiKey = $wsRequest->getParam(self::SYS_ACCESS_PASS);

    //ip address from the requestor
    $ip = self::getRequestIP();

    $credentials = array();
    $credentials['method'] = $webservice;
    $credentials['apiKey'] = $apiKey;
    $credentials['ip'] = $ip;

    return $credentials;
  }

  /**
   * check the credentials in order to grant or denied access to the service
   *
   * @param array $credentials
   *
   * @throws AccessDeniedException
   */
  public static function checkCredentials($credentials)
  {
    $webservice = $credentials['method'];
    $apiKey = $credentials['apiKey'];
    $ip = $credentials['ip'];

    $webserviceData = self::checkWebservice($webservice);
    $webserviceUserData = self::checkWebserviceUser($apiKey);
    $webserviceAccessData = self::checkWebserviceAccess($webserviceData['Webservice_Id'], $webserviceUserData['WebserviceUser_Id']);

    self::checkIPs($ip, $webserviceUserData);
  }

}

?>