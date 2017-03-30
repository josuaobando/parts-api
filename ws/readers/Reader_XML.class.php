<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class Reader_XML extends Reader
{
	
	/**
	 * @see Reader::parse()
	 * 
	 * @return XmlElement
	 */
	public function parse($data)
	{
		$xmlParser = new XmlParser();
    $xml = $xmlParser->loadXml($data);
    if (!$xml)
    {
    	$exceptionMessage = "Xml Parser: " . $xmlParser->getErrorMsg() . " (Error code: ".$xmlParser->getErrorCode().")\n\n";
    	$exceptionMessage .= $data . "\n";
    	throw new WSException($exceptionMessage);
    }
	  return $xml;
	}
	
}

?>