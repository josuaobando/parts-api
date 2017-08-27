<?php

/**
 * @author Josua
 */
class WSResponseError extends WSResponse
{

  public function __construct($systemMessage)
  {
    parent::__construct($systemMessage);
    $this->setState(WSResponse::STATE_ERROR);
  }

}

?>