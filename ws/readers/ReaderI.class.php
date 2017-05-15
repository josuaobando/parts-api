<?php

/**
 * Gustavo Granados
 * code is poetry
 */
interface ReaderI
{

  /**
   * parse the data into a specific format/object
   *
   * @param string $data
   *
   * @return mixed
   */
  public function parse($data);

}

?>