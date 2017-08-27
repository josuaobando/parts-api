<?php

/**
 * @author Josua
 */
class TblManager extends Db
{

  /**
   * singleton reference for TblManager
   *
   * @var TblManager
   */
  private static $singleton = null;

  /**
   * get a singleton instance of TblManager
   *
   * @return TblManager
   */
  public static function getInstance()
  {
    if(is_null(self::$singleton)){
      self::$singleton = new TblManager();
    }

    return self::$singleton;
  }

}

?>