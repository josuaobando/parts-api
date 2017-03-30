<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class WSException extends GeneralException 
{

  public function __construct($description) 
  {
    parent::__construct($description);
  }
  
}

?>