<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class Connector
{
	
	protected $postParams = null;
	protected $getParams = null;
	
	protected $username = null;
  protected $password = null;
  
  protected $content = null;
  protected $lastError;
  protected $lastUrl;
  
  protected $lastStats = null;
  
  /**
   * default socket timeout (seconds)
   * 
   * @var int
   */
  protected $timeout = 30;
  
  protected $newline = "\n";
  
	/**
	 * timeout in seconds
	 * 
	 * @param int $timeout
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	/**
   * set user and password
   * 
   * @param string $username
   * @param string $password
   */
  public function setUsernamePassword($username, $password)
  {
  	$this->username = $username;
  	$this->password = $password;
  }
  
	/**
   * Set the parameters for the POST method
   *
   * @param array $postParams
   */
  public function setPostParams($postParams)
  {
    $this->postParams = $postParams;
  }
  
	/**
   * Set the parameters, this function is only for GET method usage
   *
   * @param string $fileUrl
   * @param array $params
   * @return string
   */
  protected function setGetParams($fileUrl, $params)
  {
    if (is_array($params) && count($params) > 0)
    {
    	foreach ($params as $key=>$value)
      {
      	$fileUrl = str_replace("{".$key."}", urlencode($value), $fileUrl);
      }
    }
    $this->getParams = $params;

    return $fileUrl;
  }
  
	/**
	 * @param string $newline
	 */
	public function setNewline($newline)
	{
		$this->newline = $newline;
	}

	/**
   * Convert an array to string, array('key1'=>'val1', 'key2'=>'val2') == 'key1=val1&key1=val1'
   *
   * @param array $params
   * @return string
   */
  private function arrayToString($params)
  {
    $data = '';
    foreach($params as $key=>$value)
    {
    	if (is_array($value))
    	{
    		$strValue = self::arrayToString($value);
    	}
    	else
    	{
    		$strValue = $value;
    	}
    	
      $key = trim($key);
      $value = trim($strValue);

      if($key == '')
      {
        continue;
      }

      $data .= $key . '=' . urlencode($strValue) . '&';
    }

    return $data;
  }
  
  /**
   * get the stats of the last request made
   * 
   * @return array
   */
  public function getLastStats()
  {
  	return $this->lastStats;
  }
  
	/**
   * get last error occurred
   * 
   * @return string
   */
  public function getLastError()
  {
  	return $this->lastError;
  }
	
	/**
	 * get the content from an URL using cURL
	 * 
	 * @param string $URL
	 */
	private function execCurlCall($url)
  {
    ob_start();

    $content = null;
    $resURL = curl_init();

    curl_setopt($resURL, CURLOPT_URL, $url);
    curl_setopt($resURL, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($resURL, CURLOPT_NOBODY, false);
    curl_setopt($resURL, CURLOPT_FAILONERROR, true);
    curl_setopt($resURL, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($resURL, CURLOPT_FORBID_REUSE, true);
    curl_setopt($resURL, CURLOPT_FRESH_CONNECT, true);
    
    curl_setopt($resURL, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($resURL, CURLOPT_SSL_VERIFYPEER, false);
		
		curl_setopt($resURL, CURLOPT_CONNECTTIMEOUT, $this->timeout);
    curl_setopt($resURL, CURLOPT_TIMEOUT, $this->timeout);

    if ($this->postParams && is_array($this->postParams) && count($this->postParams) > 0)
    {
      curl_setopt($resURL, CURLOPT_POST, true);
      curl_setopt($resURL, CURLOPT_POSTFIELDS, $this->arrayToString($this->postParams));
    }
    
    if ($this->username && $this->password)
    {
    	curl_setopt($resURL, CURLOPT_USERPWD, $this->username . ":" . $this->password);
    }

    curl_exec ($resURL);
    $intReturnCode = curl_getinfo($resURL, CURLINFO_HTTP_CODE);
    $this->lastStats = curl_getinfo($resURL);

    if ($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304)
    {
      $this->lastError = curl_error($resURL);
    }
    else
    {
    	$this->lastError = 'Ok';
      $content = ob_get_contents();
    }

    ob_end_clean();

    curl_close($resURL);
    
    return $content;
  }
  
  /**
   * execute a soap call
   * 
   * @param string $wsdl
   * @param string $method
   * @param array $params
   * @param object $authHeader
   * @param string $namespace
   * 
   * @return object
   */
	public function execSoapCall($wsdl, $method, $params, $authHeader = null, $namespace = "http://tempuri.org/")
	{
		$options = array();
		$options['trace'] = 1;
		$options['connection_timeout'] = $this->timeout;
		$options['soap_version'] = SOAP_1_2;
		$options['compression'] = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP;
		
		$this->lastError = 'Ok';
		try
		{
			$client = @new SoapClient($wsdl, $options);
			
			if ($authHeader)
			{
				$header = new SoapHeader($namespace, "AuthHeader", $authHeader, false);
				$client->__setSoapHeaders(array($header));
			}
			
			$this->content = $client->__soapCall($method, array($params));
		}
		catch (SoapFault $ex)
		{
			$this->lastError = $ex->getMessage();
			$this->content = null;
		}
		
    $this->lastUrl = $wsdl . " ($method)";
    $this->postParams = $params;
    $this->lastStats = null;
    
    return $this->content;
	}
  
	/**
	 * it loads the url content
	 *
	 * @param string $URL
	 * @param array [optional] $params //Use GET Method
	 * 
	 * @return string
	 */
  public function loadContent($url, $params = null)
  {
    $url = $this->setGetParams($url, $params);

    $content = $this->execCurlCall($url);
    
    $this->content = $content;
    $this->lastUrl = $url;
    
    return $content;
  }
  
	/**
	 * convert an object into a string
	 * 
	 * @param object $obj
	 * 
	 * @return string
	 */
	public function objToStr($obj)
	{
		if (is_object($obj) && method_exists($obj, '__toString'))
		{
			return $obj->__toString();
		}
		
		ob_start();
		print_r($obj);
		$str = ob_get_contents();
    ob_end_clean();
    return $str;
	}
  
	/**
   * String representation of the object
   *
   * @return string
   */
  public function __toString()
  {
    $desc = "Url: " . $this->lastUrl . $this->newline;
    $desc .= "Message: " . $this->lastError . $this->newline;

    if ($this->postParams && is_array($this->postParams) && count($this->postParams) > 0)
    {
      $desc .= $this->newline . "Post Parameters:$this->newline";
      foreach ($this->postParams as $key=>$value)
      {
      	if (is_object($value))
      	{
      		$paramValue = self::objToStr($value);
      	}
      	else 
      	{
      		$paramValue = $value;
      	}
        $desc .= $key." : ".$paramValue . $this->newline;
      }
    }
    
  	if ($this->getParams && is_array($this->getParams) && count($this->getParams) > 0)
    {
      $desc .= $this->newline . "Get Parameters:$this->newline";
      foreach ($this->getParams as $key=>$value)
      {
        $desc .= $key." : ".$value . $this->newline;
      }
    }

    return $desc;
  }
}

?>