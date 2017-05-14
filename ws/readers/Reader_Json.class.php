<?php

/**
 * @author jobando
 */
class Reader_Json extends Reader
{

  /**
   * @see Reader::parse()
   *
   * @param string $data
   *
   * @return mixed
   */
  public function parse($data)
  {
    return json_decode($data);
  }

}

?>