<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class InvalidStateException extends GeneralException 
{

	public function __construct($description) 
  {
    parent::__construct($description);
  }
  
}

?>