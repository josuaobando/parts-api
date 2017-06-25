<?php

/**
 * @author Josua
 */
class AccessDeniedException extends GeneralException
{

  public function __construct($reason, $description)
  {
    parent::__construct("access denied: $reason", $description);
  }

}

?>