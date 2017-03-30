<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class Reader_Text extends Reader
{
	
	/**
	 * @see Reader::parse()
	 * 
	 * @return array
	 */
	public function parse($data)
	{
		$values = array();
		parse_str($data, $values);
		
	  return $values;
	}
	
}

?>