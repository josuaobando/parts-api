<?php

/**
 * Gustavo Granados
 * code is poetry
 */
class Reader implements IReader
{

  /**
   * @see IReader::parse()
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