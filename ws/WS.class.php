<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class WS
{
	
	/**
	 * last call stats
	 * 
	 * @var array
	 */
	protected $lastStats = null;
	
	/**
	 * last request made
	 * 
	 * @var string
	 */
	protected $lastRequest = null;
	
	/**
	 * the reader for the response
	 * 
	 * @var Reader
	 */
	protected $reader = null;
	
	/**
	 * set the reader for the respose
	 * 
	 * @param Reader $reader
	 */
	public function setReader($reader)
	{
		$this->reader = $reader;	
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
	 * get the last request as a string
	 * 
	 * @return string
	 */
	public function getLastRequest()
	{
		return $this->lastRequest;
	}

	/**
	 * execute a web service call sending the parameters using the post method
	 * 
	 * @param string $webservice
	 * @param array $params
	 * 
	 * @throws WSException
	 * 
	 * @return XmlElement
	 */
	public function execPost($webservice, $params = null)
	{
		return $this->execWS($webservice, $params, null);
	}
	
	/**
	 * execute a web service call sending the parameters using the post method
	 * 
	 * @param string $webservice
	 * @param string $method
	 * @param array $params
	 * 
	 * @throws WSException
	 * 
	 * @return XmlElement
	 */
	public function execPostMethod($webservice, $method, $params = null)
	{
		$webservice .= "/" . $method;
		return $this->execPost($webservice, $params);
	}
	
	/**
	 * execute a web service call sending the parameters using the get method
	 * 
	 * @param string $webservice
	 * @param array $params
	 * 
	 * @throws WSException
	 * 
	 * @return XmlElement
	 */
	public function execGet($webservice, $params = null)
	{
		return $this->execWS($webservice, null, $params);
	}
	
	/**
   * execute a soap call
   * 
   * @param string $wsdl
   * @param string $method
   * @param array $params
   * 
   * @return object
   */
	public function execSoapCall($wsdl, $method, $params, $authHeader = null, $namespace = "http://tempuri.org/")
	{
		$connector = new Connector();
		$connector->setTimeout(CoreConfig::WS_TIMEOUT);
		$obj = $connector->execSoapCall($wsdl, $method, $params, $authHeader, $namespace);
		$this->lastRequest = $connector->__toString();
		$this->lastStats = $connector->getLastStats();
		
		if (!$obj)
    {
      throw new WSException($connector->__toString());
    }
		
		return $obj;
	}
	
	/**
	 * this is the main function where the web service is called
	 * 
	 * @param string $webservice
	 * @param array $postParams
	 * @param array $getParams
	 * 
	 * @throws WSException
	 * 
	 * @return XmlElement
	 */
	private function execWS($webservice, $postParams, $getParams)
	{
		$connector = new Connector();
		if ($postParams && is_array($postParams))
		{
	 		$connector->setPostParams($postParams);
	 	}
	 	
	 	$connector->setTimeout(CoreConfig::WS_TIMEOUT);
		$content = $connector->loadContent($webservice, $getParams);
		$this->lastRequest = $connector->__toString();
		$this->lastStats = $connector->getLastStats();
		
		if (defined('CoreConfig::TRACK_WS_STATS_ACTIVE') &&  //tracking for webservices is defined
			  CoreConfig::TRACK_WS_STATS_ACTIVE &&             //tracking is enabled
			  
			  defined('CoreConfig::TRACK_WS_STATS_TIME') &&    //execution time to track defined
			  CoreConfig::TRACK_WS_STATS_TIME <= $this->lastStats['total_time'] && //normal execution time exceed

			  defined('CoreConfig::TRACK_WS_STATS_PATTERN') &&  //valid search pattern
			  (
				  is_null(CoreConfig::TRACK_WS_STATS_PATTERN) ||    //search pattern is set to match everything
				  preg_match(CoreConfig::TRACK_WS_STATS_PATTERN, $webservice) > 0 //webservice matches the pattern
			  )
			 )
    {
			$data = array();
			$data['request'] = $this->lastRequest;
			$data['response'] = $content;
			$data['namelookup'] = $this->lastStats['namelookup_time'];
			$data['connect'] = $this->lastStats['connect_time'];
			$data['execute'] = $this->lastStats['total_time'];
			$data['error'] = $connector->getLastError();
			MQueue::push(MQueue::TYPE_STATS_WS, $data);
    }
    
    if (!$content)
    {
      throw new WSException($connector->__toString());
    }
    
    if ($this->reader)
    {
	    return $this->reader->parse($content);
    }
    
    $defaultReader = new Reader_XML();
    return $defaultReader->parse($content);
	}
	
}

?>