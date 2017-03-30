<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class Util
{
	
  const REGEX_NUMERIC = '^[0-9]+$';
  const REGEX_ALPHANIMERIC = '^[-a-zA-Z0-9ñÑáóéíóúÁÉÍÓÚ ,.-]+$';
  const FORMAT_DATE_DISPLAY = 'd F H:i:s';
  
	/**
	 * check if the environment is development
	 */
	public static function isDEV()
  {
  	$isDev = CoreConfig::DEV;
  	
  	return $isDev === true;
  }
  
  /**
   * check if the script is running on the command line or from a browser call.
   * 
   * @return bool
   */
	public static function isCli()
	{
  	return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])); 
	}
	
	/**
	 * get server name where the application is running
	 * 
	 * @return string
	 */
	public static function getServerName()
	{
  	$server = php_uname("n");
  	return $server;
	}
	
	/**
	 * get proper new line method depending on the CLI.
	 * 
	 * @return string
	 */
	public static function getNewLine()
	{
  	return Util::isCli() ? "\n" : "<br/>"; 
	}

  /**
   * Generate a string with the values in $array separate by , (comma)
   * $array('a', 'b', 'c') => "'a', 'b', 'c'"
   *
   * @param array $array
   * @return string
   */
  public static function generateStrList($array, $empty = '-1')
  {
    if (!is_array($array) || count($array) == 0)
    {
      return $empty;
    }

    $result = "'" . implode("','", $array) . "'";
    return $result;
  }

  /**
   * Generate a string with the values in $array separate by , (comma)
   * $array('a', 'b', 'c') => "a, b, c"
   *
   * @param array $array
   * @return string
   */
  public static function generateList($array, $empty = '-1')
  {
    if (!is_array($array) || count($array) == 0)
    {
      return $empty;
    }

    $result = implode(",", $array);
    return $result;
  }
  
  /**
   * Transform an array of rows in a simple array using the key
   *
   * @param array $rows
   * @param string $key
   * @return array
   */
  public static function getRowToArray($rows, $key){
    $array = array();
    foreach ($rows as $row)
    {
      array_push($array, $row[$key]);
    }
    return $array;
  }

  /**
   * Get an array from a rows, indexing the array using a value in the row named $key
   *
   * @param array $rows
   * @param string $key
   * @return array
   */
  public static function rowsToIndexedArray($rows, $key)
  {
    $array = array();
    foreach ($rows as $row)
    {
      $array[$row[$key]] = $row;
    }
    return $array;
  }

  /**
   * Convert a array of rows form db to XmlElement
   *
   * @param array $rows
   * @param string $title
   * @param string $subTitle
   * @param array $config [optional]
   * @return XmlElement
   */
  public static function rowsToXmlList($rows, $title, $subTitle, $config = null)
  {
    $xmlList = new XmlElement($title);
    if (is_array($rows))
    {
	    foreach ($rows as $row)
	    {
	      $newElement = Util::rowToXml($row, $subTitle, $config);
	      $xmlList->addElement($newElement);
	    }
    }
    return $xmlList;
  }

  /**
   * Convert a row into a XmlElement
   *
   * @param array $row
   * @param string $title
   * @param array $config [optional]
   * @return XmlElement
   */
  public static function rowToXmlList($row, $title, $config = null)
  {
    $xmlList = new XmlElement($title);

    if ($row && is_array($row))
    {
      foreach ($row as $key=>$value)
      {
        $newElement = new XmlElement($key);
        $newElement->addAttr('value', $value);
        $xmlList->addElement($newElement);
      }
    }

    return $xmlList;
  }
  
	/**
   * Convert a row into a XmlElement
   *
   * @param array $row
   * @param string $title
   * @param bool $addRoot
   * @return XmlElement
   */
  public static function rowToXml($row, $name, $config = null)
  {
  	$escape = $config['escape'];
  	$xml = new XmlElement($name);
  	if (!is_array($row))
  	{
  		return $xml;
  	}
	  foreach ($row as $field=>$value)
	  {
	    $fieldName = $field;
	    if ($config && is_array($config['exclude']) && array_intersect(array($field), $config['exclude']))
			{
				continue;
			}
	    if ($config && is_array($config['rename']) && array_intersect_key(array($field=>$value), $config['rename']))
			{
				$fieldName = $config['rename'][$field];
			}
	    if ($config && (is_array($config['values']) && array_intersect(array($field), $config['values'])) || $config['values-all'])
			{
				$newValue = new XmlElement($fieldName);
				$newValue->setValue(Util::escapeText($value));
				$xml->addElement($newValue);
			}
			else
			{
				if ($escape)
				{
					$value = Util::escapeText($value);
				}
	      $xml->addAttr($fieldName, $value);
			}
	  }
    
    return $xml;
  }

  /**
   * Valid all the parameters
   *
   * @param variables
   * @return bool
   */
  public static function validParameters()
  {
    for ($i = 0; $i < func_num_args(); $i++)
    {
      $param = func_get_arg($i);
      if (is_null($param) or ($param == ""))
      {
        return false;
      }
    }

    return true;
  }
  
  /**
   * valid for any valid parameter
   * 
   * @return bool
   */
	public static function validParametersAny()
  {
    for ($i = 0; $i < func_num_args(); $i++)
    {
      $param = func_get_arg($i);
      if (!is_null($param) or ($param != ''))
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Call back used for the extract vars
   *
   * @param array $matches
   * @return string
   */
  public static function callBack($matches)
  {
    global $vars;
    $var = $matches[0];
    $data = split('=', $var);
    $vars[$data[0]] = $data[1];

    return $matches[0];
  }

  /**
   * Extract var from string, 'var1=value1&var2=value2&...&varX=valueX
   *
   * @param string $text
   * @return array
   */
  public static function extractVars($text)
  {
    global $vars;
    $regularExpr = '/[a-zA-Z0-9-_]+\=[^&]*/';
    $vars = array();
    preg_replace_callback($regularExpr, 'Util::callBack', $text);
    return $vars;
  }

  /**
   * Get the current time
   *
   * @return double
   */
  public static function getStartTime()
  {
    $startTime = microtime(true);
    return $startTime;
  }

  /**
   * Get the process time since $startTime
   *
   * @param double $startTime
   * @return double
   */
  public static function calculateProcessTime($startTime)
  {
    $endTime = microtime(true);
    $time = $endTime - $startTime;
    return $time;
  }

  /**
   * Get the display for $time
   *
   * @param double $time
   * 
   * @return string
   */
  public static function timeForDisplay($time)
  {
    if ($time < 60)
    {
      return number_format($time, 2) . " seconds";
    }
    $time = $time / 60;
    if ($time < 60)
    {
      return number_format($time, 2) . " minutes";
    }
    $time = $time / 60;
    if ($time < 60)
    {
      return number_format($time, 2) . " hours";
    }

    return number_format($time, 2) . " [no scale]";
  }
  
  /**
   * display seconds as hours, minutes and seconds
   *
   * @param int $time
   * 
   * @return string
   */
	public static function sec2hms($sec, $padHours = false) 
  {
    // start with a blank string
    $hms = "";
    
    // do the hours first: there are 3600 seconds in an hour, so if we divide
    // the total number of seconds by 3600 and throw away the remainder, we're
    // left with the number of hours in those seconds
    $hours = intval(intval($sec) / 3600); 

    // add hours to $hms (with a leading 0 if asked for)
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
          : $hours. ":";
    
    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($sec / 60) % 60); 

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($sec % 60); 

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
  }

  /**
   * Get a memory representation to display
   *
   * @return string
   */
  public static function getMemoryDisplay($mem = -1)
  {
    if ($mem == -1)
    {
      $mem = memory_get_usage();
    }

    if ($mem < 1024)
    {
      return number_format($mem, 2) . " bytes";
    }
    $mem = $mem / 1024;
    if ($mem < 1024)
    {
      return number_format($mem, 2) . " kb";
    }
    $mem = $mem / 1024;
    if ($mem < 1024)
    {
      return number_format($mem, 2) . " mb";
    }
    $mem = $mem / 1024;
    if ($mem < 1024)
    {
      return number_format($mem, 2) . " gb";
    }

    return number_format($mem, 2) . " [no scale]";
  }

  /**
   * Encode some or all the fields of $rows
   *
   * @param array $rows
   * @param array $fields
   * @return array
   */
  public static function encodeRows($rows, $fields)
  {
    $encodedRows = array();
    foreach ($rows as $row)
    {
      $newRow = array();
      foreach ($row as $key=>$value)
      {
        if (array_intersect(array($key), $fields))
        {
          $newRow[$key] = base64_encode($value);
        }
        else
        {
          $newRow[$key] = $value;
        }
      }
      array_push($encodedRows, $newRow);
    }

    return $encodedRows;
  }

  /**
   * Compare two arrays
   *
   * @param array $arrayA
   * @param array $arrayB
   * @return bool
   */
  public static function compareArrays($arrayA, $arrayB)
  {
    if (count($arrayA) != count($arrayB))
    {
      return false;
    }

    $size = count($arrayA);
    for ($i = 0; $i < $size; $i++)
    {
      if ($arrayA[$i] != $arrayB[$i])
      {
        return false;
      }
    }

    return true;
  }

  /**
   * convert array to string representation
   * 
   * @param array $array
   */
  public static function arrayAssocToString($array, $equal = "=", $separator = "\n", $valueId = null)
  {
    $result = "";
    foreach ($array as $key => $element)
    {
    	$value = ($valueId ? $element[$valueId] : $element);
      $result .= $key . $equal . $value . $separator;
    }
    return $result;
  }
  
  /**
   * join associative array elements with a string
   * 
   * @param array $arrayAssoc
   * @param string $keyGlue
   * @param string $elementGlue
   * 
   * @return string
   */
  public static function implodeAssoc($arrayAssoc, $keyGlue = "=", $elementGlue = "\n")
  {
  	$str = "";
		foreach ($arrayAssoc as $key=>$value) 
		{
			$str .= $key . $keyGlue . $value . $elementGlue;
		}
		$str = substr($str, 0, -1);
		return $str;
  }
  
  /**
   * explode a string into an associative array
   * 
   * @param array $str
   * @param string $keyGlue
   * @param string $elementGlue
   * 
   * @return array
   */
  public static function explodeStr($str, $keyGlue = ":", $elementGlue = "|")
  {
  	$elements = explode($elementGlue, $str);
  	$arrayAssoc = array();
  	foreach ($elements as $element) 
  	{
  		$newElement = explode($keyGlue, $element);
  		$arrayAssoc[trim($newElement[0])] = trim($newElement[1]);
  	}
  	
  	return $arrayAssoc;
  }
  
  /**
   * array representation for the options in a select input<br/>
   * 
   * @param array $array
   * @param string $id
   * @param string $valueId
   * 
   * @return array
   */
  public static function arrayAssocToSelect($array, $id = null, $valueId = null)
  {
  	$data = array();
		foreach ($array as $k=>$v)
		{
			$newValue = array();
			$newValue['id'] = $id ? $v[$id] : $k;
			$newValue['value'] = $valueId ? $v[$valueId] : $v;
			
			array_push($data, $newValue);
		}
  	return $data;
  }

  /**
   * escape string
   * 
   * @param string $s
   */
  public static function escapeText ($s)
  {
    if (!is_string($s))
    {
      $s .= '';
    }

    $characters = array();
    $characters['&'] = '&amp;';
    $characters['<'] = '&lt;';
    $characters['>'] = '&gt;';
    $characters['\''] = '&apos;';
    $characters['"'] = '&quot;';
    $characters['?'] = '&#63;';

    $characters['�'] = '&#225;';
    $characters['�'] = '&#193;';
    $characters['�'] = '&#233;';
    $characters['�'] = '&#201;';
    $characters['�'] = '&#237;';
    $characters['�'] = '&#205;';
    $characters['�'] = '&#243;';
    $characters['�'] = '&#211;';
    $characters['�'] = '&#250;';
    $characters['�'] = '&#218;';

    $characters['�'] = '&#209;';
    $characters['�'] = '&#241;';

    $characters['�'] = '&#231;';

    $result = '';
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++)
    {
      if ($characters[$s{$i}])
      {
        $result .= $characters[$s{$i}];
      }
      else
      if (ord($s{$i}) > 127)
      {
        // skipping UTF-8 escape sequences requires a bit of work
        if ((ord($s{$i}) & 0xf0) == 0xf0)
        {
          $result .= $s{$i++};
          $result .= $s{$i++};
          $result .= $s{$i++};
          $result .= $s{$i};
        } else if ((ord($s{$i}) & 0xe0) == 0xe0)
        {
          $result .= $s{$i++};
          $result .= $s{$i++};
          $result .= $s{$i};
        } else if ((ord($s{$i}) & 0xc0) == 0xc0)
        {
          $result .= $s{$i++};
          $result .= $s{$i};
        }
      } else
      {
        $result .= $s{$i};
      }
    }
    return $result;
  }

  /**
   * convert xml elements to attributes
   * 
   * @param XmlElement $xml
   * @param func $callBack
   * @return XmlElement
   */
	function xmlElementsToAttr($xml, $callBack = 0)
	{
		$result = new XmlElement($xml->getName());
		if ($xml)
		{
			$elements = $xml->getElements();
			foreach ($elements as $element) 
			{
				$elems = $element->getElements();
				$newElement = new XmlElement($element->getName());
				foreach ($elems as $elem) 
				{
					$name = $elem->getName();
					$value = $elem->getValue();
					$newElement->addAttr($name, $value);
				}
				if ($callBack)
				{
					call_user_func($callBack, $newElement);
				}
				$result->addElement($newElement);			
			}
		}
		
		return $result;
	}
	
	/**
	 * put the xml headers
	 * 
	 * @param string $format
	 */
  public static function putResponseHeaders($format = 'xml')
  {
  	if (trim($format)=='')
  	{
  		$format = 'xml';
  	}
    header('Expires: Wed, 23 Dec 1980 00:30:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: application/' . $format);
    
    if ($format == 'xml')
    {
    	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    }
  }
  
  /**
   * check if the file exists using the include_path
   * 
   * @param string $file
   * 
   * @return string
   */
	public static function file_exists($file)
	{
	  $ps = explode(PATH_SEPARATOR, ini_get('include_path'));
	  foreach($ps as $path)
	  {
	    if(file_exists($path.'/'.$file))
	    {
	    	return $path.'/'.$file;
	    }
	  }
	  if(file_exists($file))
	  { 
	  	return $file;
	  }
	  return false;
	}
	
	/**
	 * Create files and append text.
	 * 
	 * @param string $path.
	 * @param string $content.
	 * @param bool $append.
	 * 
	 * @return boolean.
	 */
	public static function writeToFile($path, $content, $append = true)
	{
		@chmod($path, 0777);
		$w = @file_put_contents($path, $content, $append ? FILE_APPEND : null);
		
		return $w > 0;
	}
  
	/**
   * checks if an array is associative or not
   * 
   * @param bool $array
   */
  public static function array_is_assoc($array)
	{
		return array_keys($array) !== range(0, count($array) - 1);
	}
	
	/**
	 * Obtain the customer's country through the web browser.
	 * 
	 * @param $ipAddress
	 * 
	 * @return String
	 */
	public static function getCountryFrom($ipAddress)
	{
		$query = CoreConfig::COUNTRY_URL.CoreConfig::IP_LICENSE_KEY."&i=".$ipAddress;
		$data = @file_get_contents($query);
		
		$search = ereg("null", $data);
		 
		if($search)
		{
			$data = "IP NOT FOUND.";
		}
		
		return $data;
	}
	
	/**
	 * get the list of directories contained in a folder
	 * 
	 * @param string $dir
	 * @param array $directories
	 * 
	 * @return array
	 */
	public static function getDirectories($dir, $directories = array())
	{
		$items = scandir($dir);
		foreach ($items as $item)
		{
			$path = $dir . DIRECTORY_SEPARATOR . $item;
			if (!is_dir($path) ||           //not a directory 
			     $item == '.' ||            //current directory
			     $item == '..' ||           //parent directory
			     substr($item, 0, 1) == '.' //hidden directory
			     )
			{
				continue;
			}
			array_push($directories, $path);
			$directories = self::getDirectories($path, $directories);
		}
		return $directories;
	}
	
	/**
	 * get the list of months
	 * 
	 * @return array
	 */
	public static function generateMonthList()
  {
  	$months = array();
  	$months['01'] = 'Jan';
  	$months['02'] = 'Feb';
  	$months['03'] = 'Mar';
  	$months['04'] = 'Apr';
  	$months['05'] = 'May';
  	$months['06'] = 'Jun';
  	$months['07'] = 'Jul';
  	$months['08'] = 'Aug';
  	$months['09'] = 'Sep';
  	$months['10'] = 'Oct';
  	$months['11'] = 'Nov';
  	$months['12'] = 'Dec';
  	return $months;
  }
  
  /**
   * get the list of countries
   * 
   * @param int $min
   * @param int $max
   * 
   * @return array
   */
	public static function generateYearList($min = 2000, $max = 2020)
  {
  	$years = array();
  	for ($i = $min ; $i <= $max; $i++)
  	{
  		$years[$i] = $i;
  	}
  	return $years;
  }
  
  /**
   * truncate a decimal value
   * 
   * @param float $amount
   * @param int $decimals
   */
  public static function truncate($amount, $decimals = 2)
  {
  	return floor($amount * pow(10, $decimals)) / pow(10, $decimals);
  }
  
  /**
	 * scratch a value, optional you could get the last X letters/digits
	 * 
	 * @param string $key
	 * @param int $digits [-1 will scratch the whole value]
	 * 
	 * @return string
	 */
	public static function scratch($value, $digits = -1)
	{
		if ($digits < 0)
		{
			$digits = 0;
		}
		$scratchedLength = strlen($value)-$digits;
		if ($scratchedLength < 0)
		{
			$scratchedLength = 0;
		}
		$scratchedValue = str_repeat("*", $scratchedLength) . substr($value, $scratchedLength, $digits);
		return $scratchedValue;
	}
	
	/**
	 * convert an object into a string
	 * 
	 * @param object $obj
	 * 
	 * @return string
	 */
	public static function objToStr($obj)
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
	
}

?>