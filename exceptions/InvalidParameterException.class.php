<?php

/**
 * @author Josua
 */
class InvalidParameterException extends GeneralException
{

  public function __construct($key, $value, $function)
  {
    parent::__construct("missing or invalid parameter: $key");
  }

}

?>