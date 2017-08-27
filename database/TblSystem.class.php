<?php

/**
 * @author Josua
 */
class TblSystem extends Db
{

  /**
   * singleton reference for TblSystem
   *
   * @var TblSystem
   */
  private static $singleton = null;

  /**
   * get a singleton instance of TblSystem
   *
   * @return TblSystem
   */
  public static function getInstance()
  {
    if(is_null(self::$singleton)){
      self::$singleton = new TblSystem();
    }

    return self::$singleton;
  }

}

?>