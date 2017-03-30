<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class InvalidParameterException extends GeneralException 
{

  public function __construct($key, $value, $function)
  {
    parent::__construct("missing or invalid parameter: $key");
  }

}

?>