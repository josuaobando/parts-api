<?php

/**
 * Gustavo Granados
 * code is poetry
 */

abstract class Reader implements IReader
{
	
	/**
	 * @see IReader::parse()
	 */
	public function parse($data)
	{
		return $data;
	}

}

?>