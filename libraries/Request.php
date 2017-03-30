<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class Request
{
	
	protected $params = null;
	
	/**
	 * it creates a new instance of Request
	 * @param array $params
	 */
	public function __construct($params = null)
	{
		$this->setParams($params);
	}
	
	/**
	 * set the request parameters
	 * @param array $params
	 */
	public function setParams($params = null)
	{
		self::clearParams();
		if ($params && is_array($params))
		{
			foreach ($params as $key=>$value) 
			{
				if (!$key || trim($key)=='')
				{
					continue;
				}
				
				$this->params[$key] = $value;
			}
		}
	}
	
	/**
	 * get one parameter from the web service request
	 * 
	 * @param string $key
	 * @param mixed $default
	 * 
	 * @return mixed
	 */
	public function getParam($key, $default = null)
	{
		$value = $this->params[$key];
		if (!is_object($value) && (!$value || trim($value)==''))
		{
			return $default;
		}
		return $value;
	}

	/**
	 * add or update a parameter in the request
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function putParam($key, $value)
	{
		$this->params[$key] = $value;	
	}
	
	/**
	 * get all request parameters
	 * 
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * clean parameters
	 */
	public function clearParams()
	{
		if (is_array($this->params))
		{
			array_splice($this->params, 0);
		}
		else
		{
			$this->params = array();
		}
	}
	
	/**
	 * convert request to string
	 */
	public function __toString()
	{
		$str = "";
		if (is_array($this->params))
		{
			foreach ($this->params as $key=>$value)
			{
				$str .= "$key=$value\n";
			}
		}
		return $str;
	}
}

?>