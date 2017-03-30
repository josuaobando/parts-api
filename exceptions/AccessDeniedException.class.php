<?php

/**
 * Gustavo Granados
 * code is poetry
 */

class AccessDeniedException extends GeneralException 
{

  public function __construct($reason, $description)
  {
    parent::__construct("access denied: $reason", $description);
  }

}

?>