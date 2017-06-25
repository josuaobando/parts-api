<?php

/**
 * @author Josua
 */
class Reader implements ReaderI
{

  /**
   * @see ReaderI::parse()
   *
   * @param string $data
   *
   * @return string
   */
  public function parse($data)
  {
    return $data;
  }

}

?>