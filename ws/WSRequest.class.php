<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class WSRequest extends Request
{
	
	/**
	 * check if the value is numeric
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireNumeric($key)
	{
		$value = $this->getParam($key);
		if (!is_numeric($value))
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the value is numeric AND is more than 0
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireNumericAndPositive($key)
	{
		$value = $this->getParam($key);
		if (!is_numeric($value) || $value <= 0)
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the parameter is null or empty;	
	 * empty only applies for string values
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireNotNullOrEmpty($key)
	{
		$value = $this->getParam($key);
		if (is_null($value) || (is_string($value) && trim($value) == ''))
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the parameter is null	
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireNotNull($key)
	{
		$value = $this->getParam($key);
		if (is_null($value))
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the parameter is an email	
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireEmailAddress($key)
	{
		$value = $this->getParam($key);
		if (!$this->isEmailAddress($value))
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the value is between the range required
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireRange($key, $min, $max)
	{
		$value = $this->getParam($key);
		if (!is_numeric($value) || $value < $min || $value > $max)
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		return $value;
	}
	
	/**
	 * check if the value matches with a specific regular expression.
	 * 
	 * @param string $key
	 * @param string $regEx
	 * 
	 * @return mixed
	 * 
	 * @throws InvalidParameterException
	 */
	public function requireRegex($key, $regEx)
	{
		$value = $this->getParam($key);

		$matched = $this->checkRegex($value, $regEx);
		
		//check if the value has some coincidence.
		if(!$matched)
		{
			throw new InvalidParameterException($key, $value, __FUNCTION__);
		}
		
		return $value;
	}
	
	/**
	 * check if the value matches with a specific regular expression.
	 * 
	 * @param string $value
	 * @param string $regEx
	 * 
	 * @return bool
	 */
	public function checkRegex($value, $regEx)
	{
		$matched = preg_match($regEx, (string)$value);
		return $matched;
	}
	
}

?>